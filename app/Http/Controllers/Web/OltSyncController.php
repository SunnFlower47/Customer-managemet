<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Olt;
use App\Services\OltSyncService;
use Illuminate\Http\Request;

class OltSyncController extends BaseController
{
    protected $syncService;

    public function __construct(OltSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Start sync for OLT
     */
    public function sync(Request $request, Olt $olt)
    {
        $syncType = $request->input('type', 'full');
        
        $syncLog = $this->syncService->sync($olt, $syncType);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'sync_log_id' => $syncLog->id,
                'message' => 'Sinkronisasi dimulai',
            ]);
        }

        return back()->with('success', 'Sinkronisasi dimulai. ID: ' . $syncLog->id);
    }

    /**
     * Get sync progress
     */
    public function getProgress(Request $request, int $syncLogId)
    {
        $syncLog = $this->syncService->getSyncProgress($syncLogId);

        if (!$syncLog) {
            return response()->json([
                'success' => false,
                'message' => 'Sync log tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $syncLog->status,
                'progress_percentage' => $syncLog->progress_percentage,
                'processed_items' => $syncLog->processed_items,
                'total_items' => $syncLog->total_items,
                'new_onus' => $syncLog->new_onus,
                'updated_onus' => $syncLog->updated_onus,
                'errors' => $syncLog->errors,
                'error_message' => $syncLog->error_message,
            ],
        ]);
    }
}
