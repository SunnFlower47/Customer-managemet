<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\OltPonPort;
use Illuminate\Support\Facades\File;

class DisableOltMockMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olt:disable-mock
                            {--force : Force disable without confirmation}
                            {--clean-test-data : Remove test OLTs with mock IPs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable OLT mock mode for production and optionally clean test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Disabling OLT Mock Mode for Production...');
        $this->newLine();

        // 1. Check current mock mode status
        $currentMockMode = config('olt.mock_mode', false);
        $this->info("Current mock mode status: " . ($currentMockMode ? "✅ ENABLED" : "❌ DISABLED"));
        $this->newLine();

        // 2. Check for OLTs with mock IPs
        $mockIps = config('olt.mock_ip_addresses', ['127.0.0.1', 'localhost']);
        $mockOlts = Olt::whereIn('ip_address', $mockIps)->get();

        if ($mockOlts->count() > 0) {
            $this->warn("⚠️  Found {$mockOlts->count()} OLT(s) with mock IP addresses:");
            foreach ($mockOlts as $olt) {
                $this->line("   - {$olt->kode_olt} ({$olt->nama}) - IP: {$olt->ip_address}");
            }
            $this->newLine();

            if ($this->option('clean-test-data')) {
                if ($this->option('force') || $this->confirm('Do you want to delete these test OLTs?', false)) {
                    foreach ($mockOlts as $olt) {
                        // Delete related data
                        Onu::where('olt_id', $olt->id)->delete();
                        OltPonPort::where('olt_id', $olt->id)->delete();
                        $olt->delete();
                        $this->info("   ✅ Deleted OLT: {$olt->kode_olt}");
                    }
                    $this->newLine();
                }
            } else {
                $this->warn("   Use --clean-test-data flag to remove these OLTs");
                $this->newLine();
            }
        } else {
            $this->info("✅ No OLTs with mock IP addresses found");
            $this->newLine();
        }

        // 3. Update config file
        $configPath = config_path('olt.php');
        if (File::exists($configPath)) {
            $configContent = File::get($configPath);

            // Check if mock_mode is already false
            if (preg_match("/'mock_mode'\s*=>\s*(true|false)/", $configContent, $matches)) {
                if ($matches[1] === 'true') {
                    if ($this->option('force') || $this->confirm('Update config/olt.php to disable mock mode?', true)) {
                        $newContent = preg_replace(
                            "/'mock_mode'\s*=>\s*true/",
                            "'mock_mode' => false",
                            $configContent
                        );
                        File::put($configPath, $newContent);
                        $this->info("✅ Updated config/olt.php: mock_mode = false");
                    }
                } else {
                    $this->info("✅ config/olt.php already has mock_mode = false");
                }
            } else {
                $this->warn("⚠️  Could not find 'mock_mode' in config/olt.php");
                $this->line("   Please manually set 'mock_mode' => false in config/olt.php");
            }
        }

        // 4. Check .env file
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            if (preg_match("/OLT_MOCK_MODE=(true|false)/i", $envContent, $matches)) {
                if (strtolower($matches[1]) === 'true') {
                    if ($this->option('force') || $this->confirm('Update .env file to set OLT_MOCK_MODE=false?', true)) {
                        $newContent = preg_replace(
                            "/OLT_MOCK_MODE=(true|false)/i",
                            "OLT_MOCK_MODE=false",
                            $envContent
                        );
                        File::put($envPath, $newContent);
                        $this->info("✅ Updated .env: OLT_MOCK_MODE=false");
                    }
                } else {
                    $this->info("✅ .env already has OLT_MOCK_MODE=false");
                }
            } else {
                $this->warn("⚠️  OLT_MOCK_MODE not found in .env");
                $this->line("   Add this line to .env: OLT_MOCK_MODE=false");
            }
        } else {
            $this->warn("⚠️  .env file not found");
        }

        $this->newLine();
        $this->info("✅ Mock mode disabled successfully!");
        $this->newLine();
        $this->line("📝 Next steps:");
        $this->line("   1. Clear config cache: php artisan config:clear");
        $this->line("   2. Verify: php artisan tinker -> config('olt.mock_mode')");
        $this->line("   3. Test with real OLT IP addresses");
        $this->newLine();
    }
}

