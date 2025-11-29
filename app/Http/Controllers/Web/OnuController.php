<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Onu;
use App\Models\Pelanggan;
use App\Models\Odp;
use App\Services\OnuManagementService;
use Illuminate\Http\Request;

class OnuController extends BaseController
{
    protected $onuService;

    public function __construct(OnuManagementService $onuService)
    {
        $this->onuService = $onuService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Onu::with(['olt', 'ponPort', 'pelanggan', 'odp']);

        $thresholdGood = (float) $request->input('signal_good', -26);
        $thresholdWarning = (float) $request->input('signal_warning', -28);

        if ($thresholdWarning > $thresholdGood) {
            [$thresholdWarning, $thresholdGood] = [$thresholdGood, $thresholdWarning];
        }

        // Filters
        if ($request->filled('olt_id')) {
            $query->where('olt_id', $request->olt_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('card') && $request->filled('port')) {
            $query->where('card', $request->card)
                  ->where('port', $request->port);
        }

        if ($request->filled('rx_power_min') && $request->filled('rx_power_max')) {
            $query->whereBetween('rx_power', [$request->rx_power_min, $request->rx_power_max]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('mac_address', 'like', "%{$search}%");
            });
        }

        $onus = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->appends($request->query());

        $totalOnus = Onu::count();
        $stats = [
            'total' => $totalOnus,
            'online' => Onu::where('status', 'online')->count(),
            'offline' => Onu::where('status', 'offline')->count(),
            'registered' => Onu::where('is_registered', true)->count(),
        ];

        $signalStats = [
            'good' => Onu::where('rx_power', '>=', $thresholdGood)->count(),
            'warning' => Onu::whereBetween('rx_power', [$thresholdWarning, $thresholdGood])->count(),
            'critical' => Onu::where('rx_power', '<', $thresholdWarning)->count(),
            'los' => Onu::where('status', 'los')->count(),
        ];

        $olts = \App\Models\Olt::active()->get();

        $thresholds = [
            'good' => $thresholdGood,
            'warning' => $thresholdWarning,
        ];

        return view('onus.index', compact('onus', 'stats', 'olts', 'signalStats', 'thresholds', 'totalOnus'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Onu $onu)
    {
        $onu->load(['olt', 'ponPort', 'pelanggan', 'odp', 'services.speedProfile']);
        
        // Get latest details from OLT
        $details = $this->onuService->getOnuDetails($onu);

        // Get bandwidth usage for this ONU
        $bandwidthData = [];
        try {
            $driver = \App\Services\OltDriverFactory::create($onu->olt);
            $bandwidthUsage = $driver->getBandwidthUsage($onu->card, $onu->port);
            if (!empty($bandwidthUsage)) {
                $bandwidthData = $bandwidthUsage[0] ?? [];
            }
        } catch (\Exception $e) {
            // Use default if error
            $bandwidthData = [
                'download' => 0,
                'upload' => 0,
            ];
        }

        // Get data for service forms
        $speedProfiles = \App\Models\SpeedProfile::active()->get();
        $vlans = \App\Models\VlanDatabase::active()->orderBy('vlan_id')->get();
        $existingServiceIds = $onu->services->pluck('service_id')->toArray();
        $availableServiceIds = array_diff([1, 2, 3, 4], $existingServiceIds);

        $pelanggans = Pelanggan::where('status', 'aktif')
            ->select('id', 'nama', 'pppoe', 'no_hp', 'odp_id')
            ->get();
        $odps = Odp::active()->select('id', 'kode_odp', 'nama')->get();
        $pelangganOdpMap = $pelanggans->pluck('odp_id', 'id');

        return view('onus.show', compact(
            'onu',
            'details',
            'bandwidthData',
            'speedProfiles',
            'vlans',
            'availableServiceIds',
            'pelanggans',
            'odps',
            'pelangganOdpMap'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Onu $onu)
    {
        $request->validate([
            'nama' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'pelanggan_id' => 'nullable|exists:pelanggans,id',
            'odp_id' => 'nullable|exists:odps,id',
            'ont_type' => 'nullable|string|max:255',
        ]);

        $onu->update($request->only(['nama', 'description', 'pelanggan_id', 'odp_id', 'ont_type']));

        return $this->redirectToRouteWithParams('onus.show', $request, 'ONU berhasil diperbarui.', ['onu' => $onu->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Onu $onu)
    {
        $onu->delete();

        return $this->redirectToRouteWithParams('onus.index', $request, 'ONU berhasil dihapus.');
    }

    /**
     * Reboot ONU
     */
    public function reboot(Request $request, Onu $onu)
    {
        $result = $this->onuService->rebootOnu($onu);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Reset ONU
     */
    public function reset(Request $request, Onu $onu)
    {
        $result = $this->onuService->resetOnu($onu);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Disable ONU
     */
    public function disable(Request $request, Onu $onu)
    {
        $result = $this->onuService->disableOnu($onu);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Enable ONU
     */
    public function enable(Request $request, Onu $onu)
    {
        $result = $this->onuService->enableOnu($onu);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}

