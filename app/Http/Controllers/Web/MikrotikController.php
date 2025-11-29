<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mikrotik;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MikrotikController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mikrotik::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('connection_status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $mikrotiks = $query->latest()->paginate(15)->appends($request->query());

        return view('mikrotiks.index', compact('mikrotiks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mikrotiks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'routeros_version' => 'required|in:v6,v7,v7.1+',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mikrotik = Mikrotik::create($request->all());

        // Test connection
        $testResult = $this->mikrotikService->testConnection($mikrotik);

        if ($testResult['success']) {
            return redirect()->route('mikrotiks.index')
                ->with('success', 'MikroTik berhasil ditambahkan dan koneksi berhasil.');
        } else {
            return redirect()->route('mikrotiks.index')
                ->with('warning', 'MikroTik berhasil ditambahkan, namun koneksi gagal: ' . $testResult['message']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mikrotik $mikrotik)
    {
        // Get dashboard data
        $resourceUsage = [];
        $activePppoeCount = 0;

        try {
            $resourceUsage = $this->mikrotikService->getResourceUsage($mikrotik);
            $activePppoeUsers = $this->mikrotikService->getActivePppoeUsers($mikrotik);
            $activePppoeCount = count($activePppoeUsers);
        } catch (\Exception $e) {
            // If connection fails, continue without data
        }

        return view('mikrotiks.show', compact('mikrotik', 'resourceUsage', 'activePppoeCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mikrotik $mikrotik)
    {
        return view('mikrotiks.edit', compact('mikrotik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mikrotik $mikrotik)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string', // Optional, only update if provided
            'routeros_version' => 'required|in:v6,v7,v7.1+',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $mikrotik->update($data);

        // Test connection if password changed or IP/port changed
        if (isset($data['password']) || $mikrotik->wasChanged(['ip_address', 'port', 'username'])) {
            $testResult = $this->mikrotikService->testConnection($mikrotik);

            if (!$testResult['success']) {
                return redirect()->back()
                    ->with('warning', 'Data berhasil diupdate, namun koneksi gagal: ' . $testResult['message']);
            }
        }

        return redirect()->route('mikrotiks.index')
            ->with('success', 'MikroTik berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mikrotik $mikrotik)
    {
        $mikrotik->delete();

        return redirect()->route('mikrotiks.index')
            ->with('success', 'MikroTik berhasil dihapus.');
    }

    /**
     * Test connection to router
     */
    public function testConnection(Mikrotik $mikrotik)
    {
        $result = $this->mikrotikService->testConnection($mikrotik);

        if ($result['success']) {
            return redirect()->back()
                ->with('success', 'Koneksi berhasil: ' . ($result['identity'] ?? 'Connected'));
        } else {
            return redirect()->back()
                ->with('error', 'Koneksi gagal: ' . $result['message']);
        }
    }

    /**
     * Search PPPoE in router
     */
    public function searchPppoe(Request $request, Mikrotik $mikrotik)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $pppoe = $this->mikrotikService->findPppoe($mikrotik, $request->username);

        if ($pppoe) {
            return redirect()->back()
                ->with('pppoe_found', $pppoe)
                ->with('success', 'PPPoE ditemukan di router.');
        } else {
            return redirect()->back()
                ->with('error', 'PPPoE tidak ditemukan di router.');
        }
    }
}
