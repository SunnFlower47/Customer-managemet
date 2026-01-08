<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mikrotik;
use App\Models\MikrotikPppoeUser;
use App\Models\Pelanggan;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MikrotikController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * List all routers
     */
    public function index()
    {
        $routers = Mikrotik::withCount('pppoeUsers')->get();
        return view('mikrotik.index', compact('routers'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('mikrotik.create');
    }

    /**
     * Store new router
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $router = Mikrotik::create($validated);

        return redirect()->route('mikrotik.index')->with('success', 'Router MikroTik berhasil ditambahkan.');
    }

    /**
     * Show edit form
     */
    public function edit(Mikrotik $mikrotik)
    {
        return view('mikrotik.edit', compact('mikrotik'));
    }

    /**
     * Update router
     */
    public function update(Request $request, Mikrotik $mikrotik)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'nullable|string', // Nullable on update
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $mikrotik->update($validated);

        return redirect()->route('mikrotik.index')->with('success', 'Router MikroTik berhasil diperbarui.');
    }

    /**
     * Delete router
     */
    public function destroy(Mikrotik $mikrotik)
    {
        $mikrotik->delete();
        return redirect()->route('mikrotik.index')->with('success', 'Router MikroTik berhasil dihapus.');
    }

    /**
     * Test Connection (AJAX)
     */
    public function testConnection(Mikrotik $mikrotik)
    {
        $result = $this->mikrotikService->testConnection($mikrotik);
        return response()->json($result);
    }

    /**
     * Sync Users from Router
     */
    public function sync(Mikrotik $mikrotik)
    {
        $result = $this->mikrotikService->syncPppoeUsers($mikrotik);
        
        if ($result['success']) {
            return redirect()->back()->with('success', "Sync berhasil! Total: {$result['total']}, Baru: {$result['new']}, Updated: {$result['updated']}");
        } else {
            return redirect()->back()->with('error', "Sync gagal: " . $result['message']);
        }
    }

    /**
     * Show detail router and its users
     */
    public function show(Mikrotik $mikrotik)
    {
        // Load relationship for the stats part of the view
        $mikrotik->load('pppoeUsers');
        
        // Paginate users for the table
        $users = $mikrotik->pppoeUsers()
            ->with('pelanggan')
            ->orderBy('username')
            ->paginate(10);

        return view('mikrotik.show', compact('mikrotik', 'users'));
    }
    
    /**
     * Show unmapped users (Belum Sinkron)
     * "Aksi Cepat" page
     */
    public function unmapped(Mikrotik $mikrotik)
    {
        $users = $mikrotik->pppoeUsers()
            ->whereNull('pelanggan_id')
            ->orderBy('username')
            ->get();
            
        return view('mikrotik.unmapped', compact('mikrotik', 'users'));
    }

    /**
     * Create Customer from PPPoE User (Form)
     */
    public function createCustomerFromPppoe(MikrotikPppoeUser $pppoeUser)
    {
        // Pass PPPoE data to the customer create view
        // You'll need to adjust your existing customer create view to accept these checks
        // or create a dedicated one.
        // For now, let's assume we use the standard create view with query params
        return redirect()->route('pelanggans.create', [
            'mikrotik_id' => $pppoeUser->mikrotik_id,
            'pppoe_username' => $pppoeUser->username,
            'pppoe_password' => $pppoeUser->password,
            'packet_name' => $pppoeUser->profile, // Changed key to generic prompt for now, user can map it
        ]);
    }
}
