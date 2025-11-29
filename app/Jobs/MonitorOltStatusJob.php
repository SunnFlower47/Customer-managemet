<?php

namespace App\Jobs;

use App\Models\Olt;
use App\Services\OltMonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonitorOltStatusJob implements ShouldQueue
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
    public function handle(OltMonitoringService $monitoringService): void
    {
        $monitoringService->monitor($this->olt, true);
    }
}
