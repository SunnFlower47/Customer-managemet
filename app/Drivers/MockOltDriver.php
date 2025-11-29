<?php

namespace App\Drivers;

use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Support\Str;

/**
 * Mock OLT Driver untuk testing tanpa perangkat fisik
 * Return data dummy yang realistis untuk semua operasi
 */
class MockOltDriver extends BaseOltDriver
{
    protected $mockData = [];

    public function __construct(Olt $olt)
    {
        parent::__construct($olt);
        $this->initializeMockData();
    }

    protected function initializeMockData(): void
    {
        // Generate mock ONUs
        $this->mockData['onus'] = [
            [
                'serial_number' => 'ZTEGC8A57F6B',
                'mac_address' => '00:11:22:33:44:55',
                'card' => 1,
                'port' => 1,
                'onu_id' => 1,
                'status' => 'online',
                'rx_power' => -22.75,
                'tx_power' => 2.5,
                'distance' => 1250,
                'vendor' => 'ZTE',
                'model' => 'F601',
            ],
            [
                'serial_number' => 'ZTEGC8AFBBCA',
                'mac_address' => '00:11:22:33:44:56',
                'card' => 1,
                'port' => 1,
                'onu_id' => 2,
                'status' => 'online',
                'rx_power' => -18.89,
                'tx_power' => 2.3,
                'distance' => 980,
                'vendor' => 'ZTE',
                'model' => 'F601',
            ],
            [
                'serial_number' => 'YYKFCA3DB402',
                'mac_address' => '00:11:22:33:44:57',
                'card' => 1,
                'port' => 2,
                'onu_id' => 1,
                'status' => 'online',
                'rx_power' => -17.90,
                'tx_power' => 2.1,
                'distance' => 850,
                'vendor' => 'Huawei',
                'model' => 'HG8245H',
            ],
            [
                'serial_number' => 'ZTEGC8A388A4',
                'mac_address' => '00:11:22:33:44:58',
                'card' => 1,
                'port' => 2,
                'onu_id' => 2,
                'status' => 'offline',
                'rx_power' => -28.50,
                'tx_power' => null,
                'distance' => null,
                'vendor' => 'ZTE',
                'model' => 'F601',
            ],
        ];
    }

    protected function connect(): bool
    {
        // Mock connection selalu berhasil
        return true;
    }

    protected function disconnect(): void
    {
        // Mock disconnect
    }

    public function getVendorName(): string
    {
        return 'MOCK';
    }

    public function getSupportedModels(): array
    {
        return ['MOCK-TEST'];
    }

    public function testConnection(): array
    {
        // Simulasi delay koneksi
        usleep(500000); // 0.5 detik
        
        return [
            'success' => true,
            'message' => 'Koneksi mock berhasil (Mode Testing)',
            'response_time' => '0.5s',
        ];
    }

    public function getSystemInfo(): array
    {
        return [
            'description' => "Mock OLT System - {$this->olt->nama}",
            'uptime' => '45 days, 12:30:15',
            'system_name' => $this->olt->nama,
            'firmware_version' => $this->olt->firmware_version ?? 'Mock v1.0.0',
            'hardware_version' => 'Mock Hardware v1.0',
            'serial_number' => 'MOCK-' . strtoupper(Str::random(8)),
        ];
    }

    public function getPonPorts(): array
    {
        return [
            [
                'card' => 1,
                'port' => 1,
                'port_number' => '1/1/1',
                'name' => 'PON 1/1/1',
                'status' => 'up',
                'onu_count' => 2,
                'max_onus' => 64,
            ],
            [
                'card' => 1,
                'port' => 2,
                'port_number' => '1/1/2',
                'name' => 'PON 1/1/2',
                'status' => 'up',
                'onu_count' => 2,
                'max_onus' => 64,
            ],
            [
                'card' => 1,
                'port' => 3,
                'port_number' => '1/1/3',
                'name' => 'PON 1/1/3',
                'status' => 'down',
                'onu_count' => 0,
                'max_onus' => 64,
            ],
        ];
    }

    public function getOnuList(int $card, int $port): array
    {
        return array_filter($this->mockData['onus'], function($onu) use ($card, $port) {
            return $onu['card'] == $card && $onu['port'] == $port;
        });
    }

    public function getOnuDetails(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        // Check if ONU exists in database
        $dbOnu = Onu::where('serial_number', $serialNumber)->first();

        return [
            'success' => true,
            'serial_number' => $onu['serial_number'],
            'mac_address' => $onu['mac_address'],
            'status' => $onu['status'],
            'rx_power' => $onu['rx_power'],
            'tx_power' => $onu['tx_power'],
            'distance' => $onu['distance'],
            'vendor' => $onu['vendor'],
            'model' => $onu['model'],
            'card' => $onu['card'],
            'port' => $onu['port'],
            'onu_id' => $onu['onu_id'],
            'uptime' => $dbOnu ? $dbOnu->uptime_seconds ?? 86400 : 86400,
            'ip_address' => $dbOnu?->ip_address ?? '192.168.1.100',
            'gateway' => $dbOnu?->gateway ?? '192.168.1.1',
            'subnet_mask' => $dbOnu?->subnet_mask ?? '255.255.255.0',
        ];
    }

    public function getStatistics(): array
    {
        return [
            'uptime' => '45 days, 12:30:15',
            'cpu_usage' => 25.5,
            'memory_usage' => 45.2,
            'temperature' => 42,
            'fan_speed' => [
                'fan1' => 2694,
                'fan2' => 2688,
            ],
            'total_ports' => $this->olt->total_ports ?? 16,
            'active_ports' => 12,
            'total_onus' => count($this->mockData['onus']),
            'online_onus' => count(array_filter($this->mockData['onus'], fn($o) => $o['status'] === 'online')),
        ];
    }

    public function getBandwidthUsage(int $card = null, int $port = null): array
    {
        $usage = [];
        
        if ($card && $port) {
            // Specific port
            $usage[] = [
                'card' => $card,
                'port' => $port,
                'download' => rand(1000000, 50000000), // Kbps
                'upload' => rand(500000, 10000000),
                'timestamp' => now()->toDateTimeString(),
            ];
        } else {
            // All ports
            foreach ($this->getPonPorts() as $ponPort) {
                $usage[] = [
                    'card' => $ponPort['card'],
                    'port' => $ponPort['port'],
                    'download' => rand(1000000, 50000000),
                    'upload' => rand(500000, 10000000),
                    'timestamp' => now()->toDateTimeString(),
                ];
            }
        }

        return $usage;
    }

    public function getAlarms(): array
    {
        return [
            [
                'type' => 'warning',
                'message' => 'ONU ZTEGC8A388A4 signal rendah',
                'severity' => 'minor',
                'timestamp' => now()->subMinutes(30)->toDateTimeString(),
            ],
            [
                'type' => 'info',
                'message' => 'Port 1/1/3 tidak aktif',
                'severity' => 'info',
                'timestamp' => now()->subHours(2)->toDateTimeString(),
            ],
        ];
    }

    public function rebootOnu(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        // Simulasi delay reboot
        usleep(300000); // 0.3 detik

        return [
            'success' => true,
            'message' => "ONU {$serialNumber} berhasil di-reboot (simulasi)",
        ];
    }

    public function resetOnu(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        usleep(300000);

        return [
            'success' => true,
            'message' => "ONU {$serialNumber} berhasil di-reset (simulasi)",
        ];
    }

    public function disableOnu(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        usleep(200000);

        return [
            'success' => true,
            'message' => "ONU {$serialNumber} berhasil di-disable (simulasi)",
        ];
    }

    public function enableOnu(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        usleep(200000);

        return [
            'success' => true,
            'message' => "ONU {$serialNumber} berhasil di-enable (simulasi)",
        ];
    }

    public function getOnuConfig(string $serialNumber): array
    {
        $onu = collect($this->mockData['onus'])->firstWhere('serial_number', $serialNumber);
        
        if (!$onu) {
            return [
                'success' => false,
                'message' => 'ONU tidak ditemukan',
            ];
        }

        return [
            'success' => true,
            'config' => [
                'wan_mode' => 'pppoe',
                'vlan_id' => 2058,
                'pppoe_username' => 'pelanggan001',
                'speed_profile' => '10Mbps',
            ],
        ];
    }

    public function discoverUnconfiguredOnus(): array
    {
        // Return some unconfigured ONUs
        return [
            [
                'serial_number' => 'ZTEGC' . strtoupper(Str::random(6)),
                'card' => 1,
                'port' => 4,
                'vendor' => 'ZTE',
                'model' => 'F601',
                'signal' => -21.5,
            ],
            [
                'serial_number' => 'YYKFC' . strtoupper(Str::random(6)),
                'card' => 1,
                'port' => 5,
                'vendor' => 'Huawei',
                'model' => 'HG8245H',
                'signal' => -19.8,
            ],
        ];
    }

    public function provisionOnu(array $data): array
    {
        // Simulasi delay provisioning
        usleep(500000); // 0.5 detik

        $onuId = rand(1, 64);

        return [
            'success' => true,
            'message' => "ONU {$data['serial_number']} berhasil di-provisioning (simulasi)",
            'onu_id' => $onuId,
        ];
    }

    public function configureService(\App\Models\Onu $onu, \App\Models\OnuService $service, array $payload = []): array
    {
        // Simulasi konfigurasi service/wifi/lan
        usleep(200000); // 0.2 detik

        return [
            'success' => true,
            'message' => "Konfigurasi service {$service->service_id} untuk ONU {$onu->serial_number} berhasil (simulasi)",
            'payload' => $payload,
        ];
    }
}

