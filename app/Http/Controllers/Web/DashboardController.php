<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Paket;
use App\Models\Penagih;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Base query for pembayarans
        $pembayaranQuery = Pembayaran::query();

        // If user is penagih, filter by their penagih_id
        if ($user->role === 'penagih') {
            $penagih = Penagih::where('user_id', $user->id)->first();
            if ($penagih) {
                $pembayaranQuery->where('penagih_id', $penagih->id);
            }
        }

        // Dashboard statistics with caching
        $stats = CacheService::getDashboardStats();
        $growthStats = CacheService::getGrowthStats();

        // Add role-specific stats
        if ($user->role === 'penagih') {
            $penagih = Penagih::where('user_id', $user->id)->first();
            if ($penagih) {
                $stats['pendapatan_bulan_ini'] = $pembayaranQuery->clone()
                    ->where('bulan_tagihan', $currentMonth)
                    ->where('tahun_tagihan', $currentYear)
                    ->where('status', 'lunas')
                    ->sum('jumlah');
                $stats['tagihan_belum_bayar'] = $pembayaranQuery->clone()
                    ->where('status', 'belum_bayar')
                    ->sum('jumlah');
            } else {
                $stats['pendapatan_bulan_ini'] = 0;
                $stats['tagihan_belum_bayar'] = 0;
            }
        } else {
            $stats['pendapatan_bulan_ini'] = Pembayaran::where('bulan_tagihan', $currentMonth)
                ->where('tahun_tagihan', $currentYear)
                ->where('status', 'lunas')
                ->sum('jumlah');
            $stats['tagihan_belum_bayar'] = Pembayaran::where('status', 'belum_bayar')->sum('jumlah');
        }

        // Add growth data to stats
        $stats['customer_growth'] = $growthStats['customers']['growth'];
        $stats['revenue_growth'] = $growthStats['revenue']['growth'];
        $stats['current_month_customers'] = $growthStats['customers']['current'];
        $stats['last_month_customers'] = $growthStats['customers']['last_month'];
        $stats['current_month_revenue'] = $growthStats['revenue']['current'];
        $stats['last_month_revenue'] = $growthStats['revenue']['last_month'];

        // Ensure all required keys exist with default values
        $stats = array_merge([
            'total_pelanggan' => 0,
            'total_paket' => 0,
            'total_penagih' => 0,
            'pendapatan_bulan_ini' => 0,
            'tagihan_belum_bayar' => 0,
            'customer_growth' => 0,
            'revenue_growth' => 0,
        ], $stats);

        // Recent pembayarans (limit to 10 for performance)
        $recentPembayarans = $pembayaranQuery->clone()
            ->with(['pelanggan:id,nama,pppoe', 'penagih:id,nama'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Status per penagih
        $statusPerPenagih = $this->getStatusPerPenagih($user);

        // Monthly revenue chart data
        $monthlyRevenue = $this->getMonthlyRevenue($user);

        return view('dashboard.index', compact('stats', 'recentPembayarans', 'statusPerPenagih', 'monthlyRevenue'));
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        CacheService::clearDashboardCache();
        return back()->with('success', 'Cache dashboard berhasil dihapus!');
    }

    private function getTotalPelanggan($user)
    {
        if ($user->role === 'penagih') {
            return Penagih::where('user_id', $user->id)
                ->withCount('pelanggans')
                ->first()
                ->pelanggans_count ?? 0;
        }
        return Pelanggan::count();
    }

    private function getStatusPerPenagih($user)
    {
        $query = Penagih::withCount(['pelanggans'])
            ->withCount(['pembayarans as tagihan_belum_bayar_count' => function($q) {
                $q->where('status', 'belum_bayar');
            }])
            ->withSum(['pembayarans as tagihan_belum_bayar_sum' => function($q) {
                $q->where('status', 'belum_bayar');
            }], 'jumlah')
            ->withSum('pembayarans as total_tagihan_sum', 'jumlah');

        if ($user->role === 'penagih') {
            $query->where('user_id', $user->id);
        }

        return $query->get()->map(function($penagih) {
            return [
                'id' => $penagih->id,
                'nama' => $penagih->nama,
                'total_pelanggan' => $penagih->pelanggans_count,
                'tagihan_belum_bayar' => $penagih->tagihan_belum_bayar_sum ?? 0,
                'total_tagihan' => $penagih->total_tagihan_sum ?? 0,
            ];
        });
    }

    private function getMonthlyRevenue($user)
    {
        $query = Pembayaran::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(jumlah) as total')
            )
            ->where('status', 'lunas')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc');

        if ($user->role === 'penagih') {
            $penagih = Penagih::where('user_id', $user->id)->first();
            if ($penagih) {
                $query->where('penagih_id', $penagih->id);
            }
        }

        return $query->get();
    }

    public function backupDatabase()
    {
        try {
            $filename = 'wifi_billing_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Create backups directory if it doesn't exist
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            // Get database configuration
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Build mysqldump command with proper escaping for Windows
            $command = 'mysqldump';
            $command .= ' --host=' . escapeshellarg($host);
            $command .= ' --port=' . escapeshellarg($port);
            $command .= ' --user=' . escapeshellarg($username);

            // Only add password if it's not empty
            if (!empty($password)) {
                $command .= ' --password=' . escapeshellarg($password);
            }

            $command .= ' --single-transaction';
            $command .= ' --routines';
            $command .= ' --triggers';
            $command .= ' --add-drop-table';
            $command .= ' --add-locks';
            $command .= ' --create-options';
            $command .= ' --disable-keys';
            $command .= ' --extended-insert';
            $command .= ' --quick';
            $command .= ' --lock-tables=false';
            $command .= ' ' . escapeshellarg($database);
            $command .= ' > ' . escapeshellarg($path) . ' 2>&1';

            // Log the command for debugging (without password)
            Log::info('Backup command', [
                'command' => str_replace($password, '***', $command),
                'user' => Auth::user()->name
            ]);

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            // Check if file was created and has content
            if (!file_exists($path)) {
                throw new \Exception('Backup file was not created. Command output: ' . implode("\n", $output));
            }

            if (filesize($path) === 0) {
                throw new \Exception('Backup file is empty. Command output: ' . implode("\n", $output));
            }

            // Log backup activity
            Log::info('Database backup created', [
                'filename' => $filename,
                'size' => filesize($path),
                'user' => Auth::user()->name
            ]);

            return response()->download($path, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name
            ]);

            return redirect()->back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function restoreDatabase(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:sql|max:102400' // Max 100MB
            ]);

            $file = $request->file('backup_file');
            $filename = 'restore_' . time() . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Create backups directory if it doesn't exist
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $file->move(dirname($path), $filename);

            // Enhanced mysql restore command
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.port', 3306),
                config('database.connections.mysql.database'),
                $path
            );

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Restore failed: ' . implode("\n", $output));
            }

            // Clean up uploaded file
            if (file_exists($path)) {
                unlink($path);
            }

            // Log restore activity
            Log::info('Database restored', [
                'original_filename' => $file->getClientOriginalName(),
                'user' => Auth::user()->name
            ]);

            return redirect()->back()->with('success', 'Database berhasil di-restore!');

        } catch (\Exception $e) {
            Log::error('Database restore failed', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name
            ]);

            return redirect()->back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    public function runSmartBills()
    {
        try {
            // Use Artisan facade to run the command
            \Illuminate\Support\Facades\Artisan::call('bills:generate-smart');

            $output = \Illuminate\Support\Facades\Artisan::output();

            return redirect()->back()->with('success', 'Smart bills command executed successfully!<br><pre>' . $output . '</pre>');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error running smart bills command: ' . $e->getMessage());
        }
    }
}
