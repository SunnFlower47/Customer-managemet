<?php

namespace App\Console\Commands;

use App\Models\Olt;
use App\Services\OltSyncService;
use Illuminate\Console\Command;

class SyncOltDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olts:sync {--olt= : OLT ID to sync (optional, syncs all if not specified)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync OLT database with OLT devices';

    /**
     * Execute the console command.
     */
    public function handle(OltSyncService $syncService)
    {
        $oltId = $this->option('olt');

        if ($oltId) {
            $olt = Olt::find($oltId);
            if (!$olt) {
                $this->error("OLT with ID {$oltId} not found.");
                return 1;
            }

            $this->info("Syncing OLT: {$olt->kode_olt}");
            $syncLog = $syncService->sync($olt);
            $this->info("Sync completed. Status: {$syncLog->status}");
        } else {
            $this->info('Syncing all OLTs...');
            $olts = Olt::active()->get();
            
            foreach ($olts as $olt) {
                $this->info("Syncing OLT: {$olt->kode_olt}");
                $syncLog = $syncService->sync($olt);
                $this->info("  Status: {$syncLog->status}, New ONUs: {$syncLog->new_onus}, Updated: {$syncLog->updated_onus}");
            }
            
            $this->info('All OLTs synced.');
        }

        return 0;
    }
}
