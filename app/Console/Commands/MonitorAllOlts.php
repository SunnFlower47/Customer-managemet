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
    public function handle(OltMonitoringService $monitoringService)
    {
        $this->info('Starting OLT monitoring...');
        
        $monitoringService->monitorAll();
        
        $this->info('OLT monitoring completed.');
    }
}
