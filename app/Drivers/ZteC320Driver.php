<?php

namespace App\Drivers;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Onu;
use App\Models\OnuService;

class ZteC320Driver extends BaseOltDriver
{
    // ZTE C320 menggunakan OID yang sedikit berbeda dari C300
    // Atau bisa menggunakan Telnet/SSH untuk command line

    public function getVendorName(): string
    {
        return 'ZTE';
    }

    public function getSupportedModels(): array
    {
        return ['C320'];
    }

    protected function connect(): bool
    {
        // C320 bisa via SNMP atau Telnet
        if ($this->olt->connection_type === 'telnet') {
            return $this->connectTelnet();
        }
        
        return $this->connectSnmp();
    }

    protected function connectSnmp(): bool
    {
        if (!function_exists('snmpget')) {
            throw new Exception('SNMP extension tidak tersedia');
        }

        try {
            $result = @snmpget(
                $this->olt->ip_address,
                $this->olt->snmp_community ?? 'public',
                '1.3.6.1.2.1.1.1.0',
                1000000,
                3
            );

            return $result !== false;
        } catch (Exception $e) {
            throw new Exception('SNMP connection failed: ' . $e->getMessage());
        }
    }

    protected function connectTelnet(): bool
    {
        try {
            $socket = @fsockopen(
                $this->olt->ip_address,
                $this->olt->port ?? 23,
                $errno,
                $errstr,
                5 // timeout 5 seconds
            );

            if (!$socket) {
                // Provide helpful error messages
                if ($errno == 110 || strpos($errstr, 'timeout') !== false) {
                    throw new Exception("Koneksi timeout: OLT tidak merespon di IP {$this->olt->ip_address} port {$this->olt->port}. Pastikan OLT dapat diakses dan port tidak terblokir firewall.");
                } elseif ($errno == 111 || strpos($errstr, 'Connection refused') !== false) {
                    throw new Exception("Koneksi ditolak: Port {$this->olt->port} tidak terbuka atau Telnet service tidak aktif di OLT. Pastikan Telnet service enabled di OLT.");
                } elseif ($errno == 113 || strpos($errstr, 'No route') !== false) {
                    throw new Exception("Host tidak dapat dijangkau: IP {$this->olt->ip_address} tidak dapat diakses dari server. Periksa routing dan pastikan OLT terhubung ke jaringan.");
                } else {
                    throw new Exception("Koneksi gagal: {$errstr} (Error code: {$errno}). Pastikan IP address, port, dan koneksi jaringan benar.");
                }
            }

            // Read initial prompt
            $response = fread($socket, 1024);
            
            // Send username
            if (empty($this->olt->username)) {
                fclose($socket);
                throw new Exception('Username tidak boleh kosong untuk koneksi Telnet.');
            }
            
            fwrite($socket, $this->olt->username . "\r\n");
            $response = fread($socket, 1024);

            // Send password
            if (empty($this->olt->decrypted_password)) {
                fclose($socket);
                throw new Exception('Password tidak boleh kosong untuk koneksi Telnet.');
            }
            
            fwrite($socket, $this->olt->decrypted_password . "\r\n");
            $response = fread($socket, 1024);

            // Check if login successful (biasanya ada prompt atau ">" atau "#")
            if (strpos($response, '>') !== false || strpos($response, '#') !== false) {
                $this->connection = $socket;
                return true;
            }

            fclose($socket);
            throw new Exception('Login gagal: Username atau password salah. Pastikan kredensial benar dan user memiliki akses Telnet.');
        } catch (Exception $e) {
            throw new Exception('Telnet connection failed: ' . $e->getMessage());
        }
    }

    protected function disconnect(): void
    {
        if ($this->connection && is_resource($this->connection)) {
            fclose($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Execute telnet command
     */
    protected function executeCommand(string $command): string
    {
        if (!$this->connection) {
            throw new Exception('Not connected');
        }

        fwrite($this->connection, $command . "\r\n");
        $response = '';
        
        // Read response (timeout handling)
        $startTime = time();
        while (time() - $startTime < 5) {
            $line = fgets($this->connection, 1024);
            if ($line === false) break;
            $response .= $line;
            
            // Check for prompt
            if (strpos($line, '>') !== false || strpos($line, '#') !== false) {
                break;
            }
        }

        return $response;
    }

    public function getSystemInfo(): array
    {
        try {
            $this->connect();

            if ($this->olt->connection_type === 'telnet') {
                // Via Telnet command
                $response = $this->executeCommand('show version');
                // Parse response
                return [
                    'info' => $response,
                    'model' => 'C320',
                    'vendor' => 'ZTE',
                ];
            } else {
                // Via SNMP (sama seperti C300)
                return parent::getSystemInfo();
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getPonPorts(): array
    {
        try {
            $this->connect();

            if ($this->olt->connection_type === 'telnet') {
                // Via Telnet: show pon port
                $response = $this->executeCommand('show pon port');
                // Parse response untuk mendapatkan list port
                return $this->parsePonPorts($response);
            } else {
                // Via SNMP
                return parent::getPonPorts();
            }
        } catch (Exception $e) {
            return [];
        }
    }

    protected function parsePonPorts(string $response): array
    {
        // Parse telnet response untuk mendapatkan port list
        // Format response berbeda per vendor, sesuaikan
        $ports = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            // Parse sesuai format ZTE C320
            // Contoh: "gpon-olt_1/1/1    up    5"
            if (preg_match('/gpon-olt_(\d+)\/(\d+)\/(\d+)\s+(\w+)\s+(\d+)/', $line, $matches)) {
                $ports[] = [
                    'card' => (int) $matches[1],
                    'port' => (int) $matches[3],
                    'port_name' => $matches[0],
                    'status' => $matches[4],
                    'onu_count' => (int) $matches[5],
                ];
            }
        }

        return $ports;
    }

    public function getOnuList(int $card, int $port): array
    {
        try {
            $this->connect();
            
            if ($this->olt->connection_type === 'telnet') {
                $response = $this->executeCommand("show onu gpon-olt_{$card}/1/{$port}");
                return $this->parseOnuList($response, $card, $port);
            } else {
                // Via SNMP (similar to C300)
                return parent::getOnuList($card, $port);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 getOnuList error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseOnuList(string $response, int $card, int $port): array
    {
        $onuList = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Robust regex to handle variations:
            // "1  ZTE-F660    ZTEGC01234567   -25.5   online"
            // "1  ZTE-F609    ZTEGC01234567   -       offline"
            // Capture groups:
            // 1: ID
            // 2: Type/Model
            // 3: SN
            // 4: RX Power (optional, might be '-' or empty)
            // 5: Status
            if (preg_match('/^(\d+)\s+([^\s]+)\s+([^\s]+)\s+([-\d\.]+|N\/A|-)\s+([a-zA-Z]+)/', $line, $matches)) {
                $rxPower = (is_numeric($matches[4])) ? (float) $matches[4] : null;
                $status = strtolower($matches[5]);
                
                // Map status to standard values
                if (stripos($status, 'working') !== false) $status = 'online';
                if (stripos($status, 'logging') !== false) $status = 'connecting';

                $onuList[] = [
                    'onu_id' => (int) $matches[1],
                    'serial_number' => $matches[3],
                    'card' => $card,
                    'port' => $port,
                    'rx_power' => $rxPower,
                    'status' => $status,
                    'model' => $matches[2],
                ];
            }
        }
        
        return $onuList;
    }

    public function getOnuDetails(string $serialNumber): array
    {
        try {
            $this->connect();
            
            if ($this->olt->connection_type === 'telnet') {
                $response = $this->executeCommand("show onu detail {$serialNumber}");
                return $this->parseOnuDetails($response, $serialNumber);
            } else {
                // Via SNMP
                return parent::getOnuDetails($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 getOnuDetails error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseOnuDetails(string $response, string $serialNumber): array
    {
        // Parse detailed ONU information from telnet response
        $details = [
            'serial_number' => $serialNumber,
            'status' => 'unknown',
            'rx_power' => null,
            'tx_power' => null,
        ];
        
        // Extract information from response
        if (preg_match('/Status:\s*(\w+)/i', $response, $matches)) {
            $details['status'] = strtolower($matches[1]);
        }
        if (preg_match('/RX Power:\s*([-\d.]+)/i', $response, $matches)) {
            $details['rx_power'] = (float) $matches[1];
        }
        if (preg_match('/TX Power:\s*([-\d.]+)/i', $response, $matches)) {
            $details['tx_power'] = (float) $matches[1];
        }
        
        return $details;
    }

    public function getBandwidthUsage(?int $card = null, ?int $port = null): array
    {
        try {
            $this->connect();
            
            if ($this->olt->connection_type === 'telnet') {
                $bandwidthData = [];
                
                if ($card && $port) {
                    $response = $this->executeCommand("show bandwidth gpon-olt_{$card}/1/{$port}");
                    $bandwidthData = $this->parseBandwidth($response, $card, $port);
                } else {
                    $ponPorts = $this->getPonPorts();
                    foreach ($ponPorts as $portData) {
                        $response = $this->executeCommand("show bandwidth gpon-olt_{$portData['card']}/1/{$portData['port']}");
                        $portBandwidth = $this->parseBandwidth($response, $portData['card'], $portData['port']);
                        $bandwidthData = array_merge($bandwidthData, $portBandwidth);
                    }
                }
                
                return $bandwidthData;
            } else {
                // Via SNMP
                return parent::getBandwidthUsage($card, $port);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 getBandwidthUsage error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseBandwidth(string $response, int $card, int $port): array
    {
        $bandwidthData = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            // Parse format: "ZTEGC01234567  100.5  50.2"
            if (preg_match('/(\S+)\s+([\d.]+)\s+([\d.]+)/', $line, $matches)) {
                $bandwidthData[] = [
                    'serial_number' => $matches[1],
                    'card' => $card,
                    'port' => $port,
                    'bandwidth' => [
                        'download' => (float) $matches[2],
                        'upload' => (float) $matches[3],
                    ],
                ];
            }
        }
        
        return $bandwidthData;
    }

    public function discoverUnconfiguredOnus(): array
    {
        try {
            $this->connect();
            
            if ($this->olt->connection_type === 'telnet') {
                $unconfigured = [];
                $response = $this->executeCommand("show unconfigured onu");
                $lines = explode("\n", $response);
                
                foreach ($lines as $line) {
                    // Parse format: "gpon-olt_1/1/1  ZTEGC01234567  -25.5"
                    if (preg_match('/gpon-olt_(\d+)\/1\/(\d+)\s+(\S+)\s+([-\d.]+)/', $line, $matches)) {
                        $unconfigured[] = [
                            'serial_number' => $matches[3],
                            'card' => (int) $matches[1],
                            'port' => (int) $matches[2],
                            'signal' => (float) $matches[4],
                            'vendor' => null,
                            'model' => null,
                        ];
                    }
                }
                
                return $unconfigured;
            } else {
                // Via SNMP
                return parent::discoverUnconfiguredOnus();
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 discoverUnconfiguredOnus error: " . $e->getMessage());
            return [];
        }
    }

    public function provisionOnu(array $data): array
    {
        try {
            $this->connect();
            
            $card = $data['card'] ?? 1;
            $port = $data['port'] ?? 1;
            $serialNumber = $data['serial_number'];
            $ontType = $data['ont_type'] ?? 'ZTE-F660';
            
            if ($this->olt->connection_type === 'telnet') {
                // Telnet command untuk register ONU
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$card}/1/{$port}\n";
                $command .= "onu {$serialNumber} type {$ontType}\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                // Check response untuk success
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false || 
                    strpos($response, 'registered') !== false) {
                    
                    // Get ONU ID after registration
                    sleep(2);
                    $onuId = $this->getOnuIdAfterRegistration($card, $port, $serialNumber);
                    
                    return [
                        'success' => true,
                        'message' => 'ONU berhasil diregistrasi',
                        'onu_id' => $onuId,
                    ];
                }
                
                throw new Exception('Registrasi gagal: ' . $response);
            } else {
                // Via SNMP (similar to C300)
                return parent::provisionOnu($data);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 provisionOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    protected function getOnuIdAfterRegistration(int $card, int $port, string $serialNumber): ?int
    {
        try {
            $onuList = $this->getOnuList($card, $port);
            foreach ($onuList as $onu) {
                if ($onu['serial_number'] === $serialNumber) {
                    return $onu['onu_id'];
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function rebootOnu(string $serialNumber): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($serialNumber);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan');
            }
            
            if ($this->olt->connection_type === 'telnet') {
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$onuData['card']}/1/{$onuData['port']}\n";
                $command .= "onu {$serialNumber} reboot\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false ||
                    strpos($response, 'rebooted') !== false) {
                    return [
                        'success' => true,
                        'message' => 'Perintah reboot berhasil dikirim',
                    ];
                }
                
                throw new Exception('Reboot gagal: ' . $response);
            } else {
                // Via SNMP
                return parent::rebootOnu($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 rebootOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function resetOnu(string $serialNumber): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($serialNumber);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan');
            }
            
            if ($this->olt->connection_type === 'telnet') {
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$onuData['card']}/1/{$onuData['port']}\n";
                $command .= "onu {$serialNumber} reset\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false ||
                    strpos($response, 'reset') !== false) {
                    return [
                        'success' => true,
                        'message' => 'Perintah reset berhasil dikirim',
                    ];
                }
                
                throw new Exception('Reset gagal: ' . $response);
            } else {
                // Via SNMP
                return parent::resetOnu($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 resetOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function disableOnu(string $serialNumber): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($serialNumber);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan');
            }
            
            if ($this->olt->connection_type === 'telnet') {
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$onuData['card']}/1/{$onuData['port']}\n";
                $command .= "onu {$serialNumber} disable\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false ||
                    strpos($response, 'disabled') !== false) {
                    return [
                        'success' => true,
                        'message' => 'ONU berhasil di-disable',
                    ];
                }
                
                throw new Exception('Disable gagal: ' . $response);
            } else {
                // Via SNMP
                return parent::disableOnu($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 disableOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function enableOnu(string $serialNumber): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($serialNumber);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan');
            }
            
            if ($this->olt->connection_type === 'telnet') {
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$onuData['card']}/1/{$onuData['port']}\n";
                $command .= "onu {$serialNumber} enable\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false ||
                    strpos($response, 'enabled') !== false) {
                    return [
                        'success' => true,
                        'message' => 'ONU berhasil di-enable',
                    ];
                }
                
                throw new Exception('Enable gagal: ' . $response);
            } else {
                // Via SNMP
                return parent::enableOnu($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 enableOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getOnuConfig(string $serialNumber): array
    {
        try {
            $this->connect();
            
            if ($this->olt->connection_type === 'telnet') {
                $response = $this->executeCommand("show onu config {$serialNumber}");
                return $this->parseOnuConfig($response, $serialNumber);
            } else {
                // Via SNMP
                return parent::getOnuConfig($serialNumber);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 getOnuConfig error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseOnuConfig(string $response, string $serialNumber): array
    {
        $config = [
            'serial_number' => $serialNumber,
            'vlan' => null,
            'pppoe' => null,
            'ip_address' => null,
        ];
        
        // Parse VLAN
        if (preg_match('/VLAN:\s*(\d+)/i', $response, $matches)) {
            $config['vlan'] = (int) $matches[1];
        }
        
        // Parse PPPoE
        if (preg_match('/PPPoE:\s*(\S+)/i', $response, $matches)) {
            $config['pppoe'] = $matches[1];
        }
        
        // Parse IP
        if (preg_match('/IP:\s*([\d.]+)/i', $response, $matches)) {
            $config['ip_address'] = $matches[1];
        }
        
        return $config;
    }

    public function configureService(Onu $onu, OnuService $service, array $payload = []): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($onu->serial_number);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan di OLT');
            }
            
            if ($this->olt->connection_type === 'telnet') {
                $command = "configure terminal\n";
                $command .= "interface gpon-olt_{$onuData['card']}/1/{$onuData['port']}\n";
                $command .= "onu {$onu->serial_number}\n";
                
                // Configure VLAN
                if ($service->vlan_id) {
                    $command .= "vlan {$service->vlan_id}\n";
                }
                
                // Configure PPPoE
                if ($service->wan_mode === 'pppoe' && $service->pppoe_username) {
                    $command .= "pppoe username {$service->pppoe_username} password {$service->pppoe_password}\n";
                }
                
                // Configure Static IP
                if ($service->wan_mode === 'static' && $service->static_ip) {
                    $command .= "ip address {$service->static_ip} {$service->static_subnet}\n";
                    $command .= "ip gateway {$service->static_gateway}\n";
                    if ($service->static_dns1) {
                        $command .= "ip dns {$service->static_dns1}\n";
                    }
                }
                
                $command .= "exit\n";
                $command .= "exit\n";
                $command .= "exit\n";
                
                $response = $this->executeCommand($command);
                
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'OK') !== false ||
                    strpos($response, 'configured') !== false) {
                    return [
                        'success' => true,
                        'message' => 'Service berhasil dikonfigurasi',
                    ];
                }
                
                throw new Exception('Konfigurasi gagal: ' . $response);
            } else {
                // Via SNMP
                return parent::configureService($onu, $service, $payload);
            }
        } catch (Exception $e) {
            Log::error("ZTE C320 configureService error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    protected function findOnuBySerial(string $serialNumber): ?array
    {
        try {
            $ponPorts = $this->getPonPorts();
            foreach ($ponPorts as $port) {
                $onuList = $this->getOnuList($port['card'], $port['port']);
                foreach ($onuList as $onu) {
                    if ($onu['serial_number'] === $serialNumber) {
                        return [
                            'card' => $onu['card'],
                            'port' => $onu['port'],
                            'onu_id' => $onu['onu_id'],
                        ];
                    }
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
}

