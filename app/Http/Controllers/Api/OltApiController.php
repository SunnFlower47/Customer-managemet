<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Olt;
use App\Models\Onu;
use App\Services\OltMonitoringService;
use App\Services\OltDriverFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OltApiController extends Controller
{
    protected $monitoringService;

    public function __construct(OltMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Get real-time data for single OLT
     */
    public function getRealtimeData(Olt $olt)
    {
        // Check cache first
        $cachedData = Cache::get("olt_data_{$olt->id}");

        if ($cachedData && $olt->last_checked_at && now()->diffInSeconds($olt->last_checked_at) < 30) {
            return response()->json([
                'success' => true,
                'cached' => true,
                'data' => $cachedData,
                'last_updated' => $olt->last_checked_at,
            ]);
        }

        // Get fresh data
        $data = $this->monitoringService->monitor($olt, true);

        return response()->json([
            'success' => true,
            'cached' => false,
            'data' => $data,
            'last_updated' => now(),
        ]);
    }

    /**
     * Get real-time data for all OLTs
     */
    public function getAllRealtimeData()
    {
        $olts = Olt::active()->get();
        $data = [];

        foreach ($olts as $olt) {
            $cachedData = Cache::get("olt_data_{$olt->id}");

            $data[] = [
                'olt_id' => $olt->id,
                'kode_olt' => $olt->kode_olt,
                'nama' => $olt->nama,
                'status' => $olt->status,
                'data' => $cachedData ?: null,
                'last_checked' => $olt->last_checked_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get ONU traffic data with history
     */
    public function getOnuTraffic(Onu $onu)
    {
        try {
            $driver = OltDriverFactory::create($onu->olt);
            $bandwidthUsage = $driver->getBandwidthUsage($onu->card, $onu->port);

            $currentData = !empty($bandwidthUsage) ? $bandwidthUsage[0] : [
                'download' => 0,
                'upload' => 0,
            ];

            // Get historical data from cache or generate from current data
            $cacheKey = "onu_traffic_history_{$onu->id}";
            $history = \Illuminate\Support\Facades\Cache::get($cacheKey, []);

            // Add current data to history
            $history[] = [
                'timestamp' => now()->toIso8601String(),
                'download' => $currentData['download'] ?? 0,
                'upload' => $currentData['upload'] ?? 0,
            ];

            // Keep only last 24 hours (144 data points if polling every 10 minutes)
            $history = array_slice($history, -144);

            // Cache for next request
            \Illuminate\Support\Facades\Cache::put($cacheKey, $history, now()->addHours(25));

            // If no history, generate sample data for display
            if (empty($history)) {
                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = now()->subHours($i);
                    $history[] = [
                        'timestamp' => $timestamp->toIso8601String(),
                        'download' => ($currentData['download'] ?? 0) * (0.8 + (rand(0, 40) / 100)), // ±20% variation
                        'upload' => ($currentData['upload'] ?? 0) * (0.8 + (rand(0, 40) / 100)),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => [
                        'download' => $currentData['download'] ?? 0,
                        'upload' => $currentData['upload'] ?? 0,
                        'download_mbps' => round(($currentData['download'] ?? 0) / 1000, 2),
                        'upload_mbps' => round(($currentData['upload'] ?? 0) / 1000, 2),
                        'timestamp' => now()->toIso8601String(),
                    ],
                    'history' => $history,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'current' => [
                        'download' => 0,
                        'upload' => 0,
                        'download_mbps' => 0,
                        'upload_mbps' => 0,
                    ],
                    'history' => [],
                ],
            ], 500);
        }
    }
}
