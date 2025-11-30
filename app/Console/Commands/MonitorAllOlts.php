<?php

namespace App\Console\Commands;

use App\Services\OltMonitoringService;
use Illuminate\Console\Command;

class MonitorAllOlts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olts:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor all OLT devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting OLT realtime monitoring...');

        $realtimeService = new \App\Services\OltRealtimeMonitoringService(
            new \App\Services\OltEventService(),
            new \App\Services\OnuEventService()
        );

        $realtimeService->monitorAllOlts();

        $this->info('OLT realtime monitoring completed.');
    }
}
