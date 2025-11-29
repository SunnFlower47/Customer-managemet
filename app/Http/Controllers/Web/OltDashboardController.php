<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Http\Request;

class OltDashboardController extends BaseController
{
    /**
     * Display OLT monitoring dashboard
     */
    public function index()
    {
        // OLT Statistics
        $totalOlts = Olt::count();
        $onlineOlts = Olt::where('status', 'online')->count();
        $offlineOlts = Olt::where('status', 'offline')->count();
        
        // ONU Statistics
        $totalOnus = Onu::count();
        $onlineOnus = Onu::where('status', 'online')->count();
        $offlineOnus = Onu::where('status', 'offline')->count();
        $losOnus = Onu::where('status', 'los')->count();
        $dyingGaspOnus = Onu::where('status', 'dying_gasp')->count();
        
        // Signal Statistics (default thresholds: Good >= -26, Warning -28 to -26, Critical < -28)
        $signalGood = Onu::where('status', 'online')
            ->whereNotNull('rx_power')
            ->where('rx_power', '>=', -26)
            ->count();
        $signalWarning = Onu::where('status', 'online')
            ->whereNotNull('rx_power')
            ->whereBetween('rx_power', [-28, -26])
            ->count();
        $signalCritical = Onu::where('status', 'online')
            ->whereNotNull('rx_power')
            ->where('rx_power', '<', -28)
            ->count();
        
        // Port Statistics
        $totalPorts = Olt::sum('total_ports') ?? 0;
        $usedPorts = Olt::sum('ports_terpakai') ?? 0;
        $availablePorts = $totalPorts - $usedPorts;
        
        $stats = [
            'total_olts' => $totalOlts,
            'online_olts' => $onlineOlts,
            'offline_olts' => $offlineOlts,
            'total_onus' => $totalOnus,
            'online_onus' => $onlineOnus,
            'offline_onus' => $offlineOnus,
            'los_onus' => $losOnus,
            'dying_gasp_onus' => $dyingGaspOnus,
            'signal_good' => $signalGood,
            'signal_warning' => $signalWarning,
            'signal_critical' => $signalCritical,
            'total_ports' => $totalPorts,
            'used_ports' => $usedPorts,
            'available_ports' => $availablePorts,
        ];

        // Recent OLTs with status
        $olts = Olt::withCount(['onus', 'ponPorts'])
            ->orderBy('last_checked_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Recent ONU activities
        $recentOnus = Onu::with(['olt', 'ponPort', 'pelanggan'])
            ->orderBy('last_online_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        
        // ONUs by status for quick overview
        $onusByStatus = Onu::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard.olt', compact('stats', 'olts', 'recentOnus', 'onusByStatus'));
    }
}
