<?php

namespace App\Console\Commands;

use App\Models\Olt;
use App\Jobs\AutoDiscoverOnuJob;
use Illuminate\Console\Command;

class CheckUnregisteredOnu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olts:check-unregistered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for unregistered ONUs and auto-discover them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for unregistered ONUs...');
        
        $olts = Olt::active()->get();
        
        foreach ($olts as $olt) {
            $this->info("Checking OLT: {$olt->kode_olt}");
            
            // Dispatch job to discover ONUs
            AutoDiscoverOnuJob::dispatch($olt);
        }
        
        $this->info('Auto-discovery jobs dispatched.');
    }
}
