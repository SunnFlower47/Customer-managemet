<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Olt;
use App\Models\OltPonPort;
use App\Models\Onu;
use App\Models\OnuService;
use App\Models\VlanDatabase;
use App\Models\SpeedProfile;
use App\Models\Pelanggan;
use App\Models\Odp;
use Illuminate\Support\Str;

class OltTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test OLT data...');

        // Create VLANs
        $vlans = [
            ['vlan_id' => 2058, 'nama' => 'VLAN Internet', 'purpose' => 'Internet', 'is_active' => true],
            ['vlan_id' => 100, 'nama' => 'VLAN Management', 'purpose' => 'Management', 'is_active' => true],
            ['vlan_id' => 200, 'nama' => 'VLAN VoIP', 'purpose' => 'VoIP', 'is_active' => true],
        ];

        foreach ($vlans as $vlan) {
            VlanDatabase::updateOrCreate(
                ['vlan_id' => $vlan['vlan_id']],
                $vlan
            );
        }

        // Create Speed Profiles
        $speedProfiles = [
            ['nama' => '10 Mbps', 'download_speed' => 10000, 'upload_speed' => 5000, 'is_active' => true],
            ['nama' => '20 Mbps', 'download_speed' => 20000, 'upload_speed' => 10000, 'is_active' => true],
            ['nama' => '50 Mbps', 'download_speed' => 50000, 'upload_speed' => 25000, 'is_active' => true],
            ['nama' => '100 Mbps', 'download_speed' => 100000, 'upload_speed' => 50000, 'is_active' => true],
        ];

        foreach ($speedProfiles as $profile) {
            SpeedProfile::updateOrCreate(
                ['nama' => $profile['nama']],
                $profile
            );
        }

        // Create Mock OLT (gunakan IP 127.0.0.1 untuk trigger mock mode)
        $olt = Olt::updateOrCreate(
            ['kode_olt' => 'OLT-TEST-001'],
            [
                'nama' => 'OLT Testing (Mock Mode)',
                'ip_address' => '127.0.0.1', // IP ini akan trigger MockOltDriver
                'port' => 161,
                'snmp_community' => 'public',
                'vendor' => 'MOCK',
                'model' => 'MOCK-TEST',
                'firmware_version' => 'Mock v1.0.0',
                'status' => 'online',
                'connection_type' => 'snmp',
                'total_ports' => 16,
                'ports_terpakai' => 8,
                'onu_terhubung' => 12,
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'alamat' => 'Jakarta, Indonesia (Mock Location)',
            ]
        );

        $this->command->info("Created OLT: {$olt->nama}");

        // Create PON Ports
        $ponPorts = [];
        for ($port = 1; $port <= 8; $port++) {
            $ponPort = OltPonPort::updateOrCreate(
                [
                    'olt_id' => $olt->id,
                    'card' => 1,
                    'port' => $port,
                ],
                [
                    'port_name' => "PON 1/1/{$port}",
                    'status' => $port <= 6 ? 'up' : 'down',
                    'onu_count' => $port <= 6 ? rand(1, 3) : 0,
                ]
            );
            $ponPorts[] = $ponPort;
        }

        // Create Pelanggan & ODP (jika belum ada)
        // Note: Pelanggan butuh pppoe (unique) dan paket_id, jadi kita skip jika tidak ada paket
        $pelanggan = null;
        $paket = \App\Models\Paket::where('aktif', true)->first();
        if ($paket) {
            $pelanggan = Pelanggan::firstOrCreate(
                ['pppoe' => 'test001'],
                [
                    'nama' => 'Pelanggan Test 001',
                    'alamat' => 'Jl. Test No. 1',
                    'no_hp' => '081234567890',
                    'paket_id' => $paket->id,
                    'tanggal_mulai' => now(),
                    'status' => 'aktif',
                ]
            );
        }

        $odp = Odp::firstOrCreate(
            ['kode_odp' => 'ODP-TEST-001'],
            [
                'nama' => 'ODP Test 001',
                'alamat' => 'Jl. Test No. 1',
                'olt_id' => $olt->id,
                'status' => 'aktif',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ]
        );

        // Create ONUs
        $onuData = [
            [
                'serial_number' => 'ZTEGC8A57F6B',
                'mac_address' => '00:11:22:33:44:55',
                'nama' => 'ONU Test 001',
                'card' => 1,
                'port' => 1,
                'status' => 'online',
                'rx_power' => -22.75,
                'tx_power' => 2.5,
                'vendor' => 'ZTE',
                'model' => 'F601',
                'ont_type' => 'GPON',
            ],
            [
                'serial_number' => 'ZTEGC8AFBBCA',
                'mac_address' => '00:11:22:33:44:56',
                'nama' => 'ONU Test 002',
                'card' => 1,
                'port' => 1,
                'status' => 'online',
                'rx_power' => -18.89,
                'tx_power' => 2.3,
                'vendor' => 'ZTE',
                'model' => 'F601',
                'ont_type' => 'GPON',
            ],
            [
                'serial_number' => 'YYKFCA3DB402',
                'mac_address' => '00:11:22:33:44:57',
                'nama' => 'ONU Test 003',
                'card' => 1,
                'port' => 2,
                'status' => 'online',
                'rx_power' => -17.90,
                'tx_power' => 2.1,
                'vendor' => 'Huawei',
                'model' => 'HG8245H',
                'ont_type' => 'GPON',
            ],
            [
                'serial_number' => 'ZTEGC8A388A4',
                'mac_address' => '00:11:22:33:44:58',
                'nama' => 'ONU Test 004',
                'card' => 1,
                'port' => 2,
                'status' => 'offline',
                'rx_power' => -28.50,
                'tx_power' => null,
                'vendor' => 'ZTE',
                'model' => 'F601',
                'ont_type' => 'GPON',
            ],
        ];

        foreach ($onuData as $index => $onuInfo) {
            $ponPort = $ponPorts[($onuInfo['port'] - 1)] ?? $ponPorts[0];
            
            $onu = Onu::updateOrCreate(
                ['serial_number' => $onuInfo['serial_number']],
                array_merge($onuInfo, [
                    'olt_id' => $olt->id,
                    'olt_pon_port_id' => $ponPort->id,
                    'pelanggan_id' => $index === 0 ? $pelanggan->id : null,
                    'odp_id' => $index === 0 ? $odp->id : null,
                    'is_active' => true,
                    'is_registered' => true,
                    'last_online_at' => $onuInfo['status'] === 'online' ? now()->subMinutes(rand(1, 60)) : null,
                    'last_offline_at' => $onuInfo['status'] === 'offline' ? now()->subDays(rand(1, 7)) : null,
                    'last_synced_at' => now()->subMinutes(rand(1, 30)),
                ])
            );

            // Create ONU Service
            if ($index < 3) { // Create service for first 3 ONUs
                $vlan = VlanDatabase::where('vlan_id', 2058)->first();
                $speedProfile = SpeedProfile::where('nama', '20 Mbps')->first();
                $usernameIndex = $index + 1;

                OnuService::updateOrCreate(
                    [
                        'onu_id' => $onu->id,
                        'service_id' => 1,
                    ],
                    [
                        'wan_mode' => 'pppoe',
                        'pppoe_username' => "pelanggan{$usernameIndex}",
                        'pppoe_password' => 'test123',
                        'vlan_id' => $vlan?->vlan_id ?? 2058,
                        'vlan_tagged' => true,
                        'speed_profile_id' => $speedProfile?->id,
                        'download_speed' => $speedProfile?->download_speed ?? 20000,
                        'upload_speed' => $speedProfile?->upload_speed ?? 10000,
                        'is_active' => true,
                    ]
                );
            }

            $this->command->info("Created ONU: {$onu->serial_number}");
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('');
        $this->command->info('Mock OLT Info:');
        $this->command->info("  - IP: 127.0.0.1 (akan otomatis menggunakan MockOltDriver)");
        $this->command->info("  - Kode: {$olt->kode_olt}");
        $this->command->info("  - Total ONUs: " . Onu::where('olt_id', $olt->id)->count());
    }
}

