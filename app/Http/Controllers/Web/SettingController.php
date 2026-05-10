<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SettingController extends Controller
{
    // Middleware sudah diterapkan di routes

    /**
     * Show settings dashboard
     */
    public function index()
    {
        $companyProfile = CompanyProfile::first();
        $backups = $this->getBackupFiles();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('settings.index', [
            'companyProfile' => $companyProfile,
            'roles' => $roles,
            'permissions' => $permissions,
            'backups' => $backups,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


    /**
     * Update company profile
     */
    public function updateCompanyProfile(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_lengkap_perusahaan' => 'nullable|string|max:255',
            'inisial_perusahaan' => 'nullable|string|max:10',
            'alamat' => 'required|string',
            'nomor_kontak' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'dana_phone' => 'nullable|string|max:20',
            'mandiri_account' => 'nullable|string|max:50',
            'mandiri_account_name' => 'nullable|string|max:255',
            'payment_whatsapp' => 'nullable|string|max:20',
            'email_support' => 'required|email',
            'website' => 'nullable|url',
            'deskripsi' => 'nullable|string',
            'payment_code_prefix' => 'required|string|max:3|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ppn_persen' => 'nullable|numeric|min:0|max:100',
            'bhp_persen' => 'nullable|numeric|min:0|max:100',
            'uso_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        $companyProfile = CompanyProfile::firstOrNew();

        $updateData = [
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_lengkap_perusahaan' => $request->nama_lengkap_perusahaan,
            'inisial_perusahaan' => $request->inisial_perusahaan,
            'alamat' => $request->alamat,
            'nomor_kontak' => $request->nomor_kontak,
            'whatsapp' => $request->whatsapp,
            'dana_phone' => $request->dana_phone,
            'mandiri_account' => $request->mandiri_account,
            'mandiri_account_name' => $request->mandiri_account_name,
            'payment_whatsapp' => $request->payment_whatsapp,
            'email_support' => $request->email_support,
            'website' => $request->website,
            'deskripsi' => $request->deskripsi,
            'payment_code_prefix' => strtoupper($request->payment_code_prefix),
            'ppn_persen' => $request->ppn_persen ?? 11.00,
            'bhp_persen' => $request->bhp_persen ?? 0.50,
            'uso_persen' => $request->uso_persen ?? 1.25,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($companyProfile->logo_path) {
                Storage::delete($companyProfile->logo_path);
            }

            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $updateData['logo_path'] = $logoPath;
        }

        $companyProfile->fill($updateData);
        $companyProfile->save();

        // Clear cache after update
        \App\Services\CacheService::clearCompanyProfileCache();

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Trigger Laravel backup (database only)
     */
    public function createBackup(Request $request)
    {
        try {
            Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $latestBackup = $this->getBackupFiles()->first();

            $message = 'Backup database berhasil dibuat dan disimpan pada storage privat.';
            if ($latestBackup) {
                $message .= ' File: ' . $latestBackup['filename'];
            }

            Log::info('Laravel backup created successfully', [
                'filename' => $latestBackup['filename'] ?? null,
                'user' => Auth::user()->name ?? 'system',
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name ?? 'system',
            ]);

            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file from private storage
     */
    public function downloadBackup(string $filename)
    {
        $path = $this->findBackupPath($filename);

        if (!$path) {
            abort(404, 'File backup tidak ditemukan.');
        }

        $disk = Storage::disk('local');

        return response()->download($disk->path($path), basename($filename));
    }

    /**
     * List available backup archives stored through Laravel Backup
     */
    private function getBackupFiles(): Collection
    {
        $disk = Storage::disk('local');
        $folder = 'Laravel';

        if (!$disk->exists($folder)) {
            return collect();
        }

        return collect($disk->files($folder))
            ->filter(fn ($path) => Str::endsWith(strtolower($path), '.zip'))
            ->map(function ($path) use ($disk) {
                $size = $disk->size($path);
                // Set timezone ke Asia/Jakarta agar sesuai dengan server time
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($path))
                    ->setTimezone('Asia/Jakarta');

                return [
                    'path' => $path,
                    'filename' => basename($path),
                    'size' => $size,
                    'size_human' => $this->formatBytes($size),
                    'last_modified' => $lastModified,
                ];
            })
            ->sortByDesc('last_modified')
            ->values();
    }

    /**
     * Resolve storage path for given backup filename
     */
    private function findBackupPath(string $filename): ?string
    {
        $filename = basename($filename);
        $disk = Storage::disk('local');
        $folder = 'Laravel';

        if (!$disk->exists($folder)) {
            return null;
        }

        return collect($disk->files($folder))
            ->first(fn ($path) => basename($path) === $filename);
    }

    /**
     * Format bytes helper
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return back()->with('success', 'Role berhasil dibuat.');
    }

    /**
     * Update role data
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Update role permissions from modal
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        try {
            $role->syncPermissions($request->permissions ?? []);
            return redirect()->route('settings.index')->with('success', 'Permission role berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->with('error', 'Gagal memperbarui permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        if ($role->users()->count() > 0) {
            $users = $role->users->pluck('name')->join(', ');
            return redirect()->route('settings.index', ['tab' => 'roles'])->withErrors([
                'role' => "Role '{$role->name}' tidak dapat dihapus karena masih digunakan oleh user: {$users}. Silakan ubah role user tersebut terlebih dahulu.",
            ]);
        }

        $role->delete();

        return redirect()->route('settings.index', ['tab' => 'roles'])->with('success', "Role '{$role->name}' berhasil dihapus.");
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Role berhasil ditetapkan ke user.');
    }
}
