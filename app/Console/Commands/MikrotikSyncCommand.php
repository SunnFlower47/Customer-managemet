<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mikrotik;
use App\Services\MikrotikService;

class MikrotikSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikrotik:sync {--id= : Optional ID of specific router to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize PPPoE users from MikroTik routers to database';

    /**
     * Execute the console command.
     */
    public function handle(MikrotikService $mikrotikService)
    {
        $routerId = $this->option('id');

        if ($routerId) {
            $routers = Mikrotik::where('id', $routerId)->get();
        } else {
            $routers = Mikrotik::all();
        }

        $this->info("Starting synchronization via Socket...");

        foreach ($routers as $router) {
            $this->info("Syncing Router: {$router->nama} ({$router->ip_address})...");
            
            try {
                $result = $mikrotikService->syncPppoeUsers($router);
                
                if ($result['success']) {
                    $this->info("✓ Success! Total: {$result['total']}, New: {$result['new']}, Updated: {$result['updated']}, Deleted: {$result['deleted']}");
                } else {
                    $this->error("✗ Failed: " . $result['message']);
                }

            } catch (\Exception $e) {
                $this->error("✗ Exception: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info('Synchronization completed.');
    }
}
