<?php

namespace App\Jobs;

use App\Models\Olt;
use App\Models\Onu;
use App\Services\OltSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoDiscoverOnuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $olt;

    /**
     * Create a new job instance.
     */
    public function __construct(Olt $olt)
    {
        $this->olt = $olt;
    }

    /**
     * Execute the job.
     */
    public function handle(OltSyncService $syncService): void
    {
        // Sync OLT to discover new ONUs
        $syncService->sync($this->olt, 'incremental');
    }
}
