<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Olt;
use App\Services\OltMonitoringService;
use App\Services\OltDriverFactory;
use Illuminate\Http\Request;

class OltController extends BaseController
{
    protected $monitoringService;

    public function __construct(OltMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Olt::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->vendor);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_olt', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $olts = $query->withCount(['onus', 'ponPorts', 'odps'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get statistics
        $stats = [
            'total' => Olt::count(),
            'online' => Olt::where('status', 'online')->count(),
            'offline' => Olt::where('status', 'offline')->count(),
            'error' => Olt::where('status', 'error')->count(),
        ];

        $supportedVendors = OltDriverFactory::getSupportedVendors();

        return view('olts.index', compact('olts', 'stats', 'supportedVendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supportedVendors = OltDriverFactory::getSupportedVendors();
        return view('olts.create', compact('supportedVendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'kode_olt.required' => 'Kode OLT wajib diisi.',
            'kode_olt.unique' => 'Kode OLT sudah digunakan. Gunakan kode yang berbeda.',
            'kode_olt.max' => 'Kode OLT maksimal 255 karakter.',
            'nama.required' => 'Nama OLT wajib diisi.',
            'nama.max' => 'Nama OLT maksimal 255 karakter.',
            'ip_address.required' => 'IP Address wajib diisi.',
            'ip_address.ip' => 'Format IP Address tidak valid. Gunakan format IPv4 (contoh: 192.168.1.100).',
            'port.integer' => 'Port harus berupa angka.',
            'port.min' => 'Port minimal 1.',
            'port.max' => 'Port maksimal 65535.',
            'connection_type.required' => 'Tipe koneksi wajib dipilih.',
            'connection_type.in' => 'Tipe koneksi tidak valid. Pilih: SNMP, Telnet, SSH, atau API.',
            'api_endpoint.required_if' => 'API Endpoint wajib diisi jika menggunakan tipe koneksi API.',
            'api_endpoint.url' => 'Format API Endpoint tidak valid. Gunakan format URL lengkap (contoh: http://192.168.1.100/api).',
            'username.required_if' => 'Username wajib diisi untuk tipe koneksi Telnet, SSH, atau API.',
            'password.required_if' => 'Password wajib diisi untuk tipe koneksi Telnet, SSH, atau API.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 sampai 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 sampai 180.',
        ];

        try {
            $request->validate([
                'kode_olt' => 'required|string|max:255|unique:olts,kode_olt',
                'nama' => 'required|string|max:255',
                'ip_address' => 'required|ip',
                'port' => 'nullable|integer|min:1|max:65535',
                'snmp_community' => 'nullable|string|max:255',
                'snmp_version' => 'nullable|in:1,2c,3',
                'vendor' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'connection_type' => 'required|in:snmp,telnet,ssh,api',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'alamat' => 'nullable|string',
                'description' => 'nullable|string',
                'total_ports' => 'nullable|integer|min:0',
                'api_endpoint' => 'nullable|url|required_if:connection_type,api',
                'username' => 'nullable|string|required_if:connection_type,telnet|required_if:connection_type,ssh|required_if:connection_type,api',
                'password' => 'nullable|string|required_if:connection_type,telnet|required_if:connection_type,ssh|required_if:connection_type,api',
            ], $messages);

            $data = $request->all();

            // Encrypt password if provided
            if (!empty($data['password'])) {
                $data['password'] = encrypt($data['password']);
            } else {
                unset($data['password']);
            }

            $olt = Olt::create($data);

            // Test connection
            $testResult = $this->monitoringService->monitor($olt, true);
            
            if (!$testResult || (isset($testResult['success']) && !$testResult['success'])) {
                $errorMsg = isset($testResult['message']) ? $testResult['message'] : 'Gagal melakukan test koneksi ke OLT.';
                return back()->withInput()->with('error', "OLT berhasil ditambahkan, namun test koneksi gagal: {$errorMsg}. Silakan periksa kredensial dan pastikan OLT dapat diakses.");
            }

            return $this->redirectToRouteWithParams('olts.index', $request, 'OLT berhasil ditambahkan dan koneksi berhasil diverifikasi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('OLT Store Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan OLT: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Olt $olt)
    {
        $olt->load(['ponPorts.onus', 'onus.services', 'odps', 'syncLogs' => function($q) {
            $q->latest()->limit(10);
        }]);
        
        // Get cached monitoring data or fetch fresh
        $monitoringData = $this->monitoringService->getCachedData($olt);
        
        if (!$monitoringData) {
            $monitoringData = $this->monitoringService->monitor($olt, true);
        }

        return view('olts.show', compact('olt', 'monitoringData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Olt $olt)
    {
        // Decrypt password for editing
        if ($olt->password) {
            $olt->password = $olt->decrypted_password;
        }

        $supportedVendors = OltDriverFactory::getSupportedVendors();
        return view('olts.edit', compact('olt', 'supportedVendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Olt $olt)
    {
        $request->validate([
            'kode_olt' => 'required|string|max:255|unique:olts,kode_olt,' . $olt->id,
            'nama' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => 'nullable|in:1,2c,3',
            'vendor' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'connection_type' => 'required|in:snmp,telnet,ssh,api',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'description' => 'nullable|string',
            'total_ports' => 'nullable|integer|min:0',
            'api_endpoint' => 'nullable|url|required_if:connection_type,api',
            'username' => 'nullable|string|required_if:connection_type,telnet|required_if:connection_type,ssh|required_if:connection_type,api',
            'password' => 'nullable|string|required_if:connection_type,telnet|required_if:connection_type,ssh|required_if:connection_type,api',
        ]);

        $data = $request->all();

        // Encrypt password if provided and changed
        if (!empty($data['password']) && $data['password'] !== $olt->decrypted_password) {
            $data['password'] = encrypt($data['password']);
        } else {
            unset($data['password']); // Keep existing encrypted password
        }

        $olt->update($data);

        // Test connection after update
        $this->monitoringService->monitor($olt, true);

        return $this->redirectToRouteWithParams('olts.index', $request, 'OLT berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Olt $olt)
    {
        // Check if OLT has ONUs
        if ($olt->onus()->count() > 0) {
            return redirect()->route('olts.index')
                ->with('error', 'OLT tidak dapat dihapus karena masih memiliki ONU terhubung.');
        }

        $olt->delete();

        return $this->redirectToRouteWithParams('olts.index', $request, 'OLT berhasil dihapus.');
    }

    /**
     * Test connection to OLT
     */
    public function testConnection(Request $request, Olt $olt)
    {
        $result = $this->monitoringService->monitor($olt, true);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message'] ?? ($result['success'] ? 'Koneksi berhasil' : 'Koneksi gagal'));
    }

    /**
     * Monitor all OLTs
     */
    public function monitorAll(Request $request)
    {
        $this->monitoringService->monitorAll();

        return back()->with('success', 'Monitoring semua OLT selesai.');
    }
}
