<?php

namespace App\Jobs;

use App\Models\Olt;
use App\Services\OltSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncOltDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $olt;
    protected $syncType;

    /**
     * Create a new job instance.
     */
    public function __construct(Olt $olt, string $syncType = 'full')
    {
        $this->olt = $olt;
        $this->syncType = $syncType;
    }

    /**
     * Execute the job.
     */
    public function handle(OltSyncService $syncService): void
    {
        $syncService->sync($this->olt, $this->syncType);
    }
}
