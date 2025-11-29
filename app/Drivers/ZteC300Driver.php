<?php

namespace App\Drivers;

use Exception;
use Illuminate\Support\Facades\Log;

class ZteC300Driver extends BaseOltDriver
{
    // ZTE C300 SNMP OIDs
    const OID_SYSTEM_DESCRIPTION = '1.3.6.1.2.1.1.1.0';
    const OID_SYSTEM_UPTIME = '1.3.6.1.2.1.1.3.0';
    const OID_SYSTEM_NAME = '1.3.6.1.2.1.1.5.0';
    
    // ZTE C300 Specific OIDs
    const OID_ZTE_PON_PORT_COUNT = '1.3.6.1.4.1.3902.1015.1.1.1.1.1.0';
    const OID_ZTE_ONU_TABLE = '1.3.6.1.4.1.3902.1015.1.1.1.1.2';
    const OID_ZTE_ONU_SERIAL = '1.3.6.1.4.1.3902.1015.1.1.1.1.3';
    const OID_ZTE_ONU_SIGNAL = '1.3.6.1.4.1.3902.1015.1.1.1.1.4';
    const OID_ZTE_ONU_STATUS = '1.3.6.1.4.1.3902.1015.1.1.1.1.5';
    const OID_ZTE_ONU_REGISTER = '1.3.6.1.4.1.3902.1015.1.1.1.1.6';
    const OID_ZTE_ONU_REBOOT = '1.3.6.1.4.1.3902.1015.1.1.1.1.7';
    const OID_ZTE_ONU_DISABLE = '1.3.6.1.4.1.3902.1015.1.1.1.1.8';
    const OID_ZTE_ONU_ENABLE = '1.3.6.1.4.1.3902.1015.1.1.1.1.9'; // Same as disable but with value 0
    const OID_ZTE_ONU_BANDWIDTH = '1.3.6.1.4.1.3902.1015.1.1.1.1.10';

    public function getVendorName(): string
    {
        return 'ZTE';
    }

    public function getSupportedModels(): array
    {
        return ['C300'];
    }

    protected function connect(): bool
    {
        // Test SNMP connection
        if (!function_exists('snmpget')) {
            throw new Exception('SNMP extension tidak tersedia. Install php-snmp extension dengan perintah: sudo apt-get install php-snmp (Ubuntu/Debian) atau sudo yum install php-snmp (CentOS/RHEL).');
        }

        try {
            $snmpVersion = $this->olt->snmp_version ?? '2c';
            $result = $this->snmpGet(
                self::OID_SYSTEM_DESCRIPTION,
                $snmpVersion
            );

            if ($result === false) {
                $lastError = error_get_last();
                $errorDetail = $lastError ? $lastError['message'] : 'Unknown SNMP error';
                
                // Provide helpful error messages
                if (strpos($errorDetail, 'timeout') !== false || strpos($errorDetail, 'No response') !== false) {
                    throw new Exception('Timeout: OLT tidak merespon. Pastikan IP address benar (' . $this->olt->ip_address . '), port tidak terblokir firewall, dan OLT dapat diakses dari server.');
                } elseif (strpos($errorDetail, 'authentication') !== false || strpos($errorDetail, 'community') !== false) {
                    throw new Exception('SNMP Community string salah. Pastikan community string benar (biasanya "public" untuk read).');
                } elseif (strpos($errorDetail, 'Host unreachable') !== false || strpos($errorDetail, 'Network is unreachable') !== false) {
                    throw new Exception('Host tidak dapat dijangkau. Pastikan IP address benar dan OLT terhubung ke jaringan yang sama atau dapat diakses via VPN/routing.');
                } else {
                    throw new Exception('Koneksi SNMP gagal: ' . $errorDetail . '. Periksa IP address, port, dan community string.');
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('SNMP connection failed: ' . $e->getMessage());
        }
    }

    protected function disconnect(): void
    {
        // SNMP is stateless, no need to disconnect
    }

    public function getSystemInfo(): array
    {
        try {
            $this->connect();

            $systemDesc = $this->snmpGet(self::OID_SYSTEM_DESCRIPTION);
            $systemName = $this->snmpGet(self::OID_SYSTEM_NAME);
            $uptime = $this->snmpGet(self::OID_SYSTEM_UPTIME);

            return [
                'description' => $systemDesc ?: 'Unknown',
                'name' => $systemName ?: 'Unknown',
                'uptime' => $uptime ?: '0',
                'ip_address' => $this->olt->ip_address,
                'model' => 'C300',
                'vendor' => 'ZTE',
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPonPorts(): array
    {
        try {
            $this->connect();

            $ponPorts = [];
            
            // Get PON port count (contoh OID, sesuaikan dengan dokumentasi ZTE C300)
            $portCount = $this->snmpGet(self::OID_ZTE_PON_PORT_COUNT);

            if ($portCount !== false) {
                $count = (int) $portCount;
                
                // Walk through each port
                for ($i = 1; $i <= $count; $i++) {
                    // Get port status (contoh OID, sesuaikan)
                    $portStatus = $this->snmpGet(self::OID_ZTE_PON_PORT_COUNT . ".{$i}");

                    $ponPorts[] = [
                        'card' => 1,
                        'port' => $i,
                        'port_name' => "gpon-olt_1/1/{$i}",
                        'status' => $portStatus ? 'up' : 'down',
                        'onu_count' => $this->getOnuCountForPort(1, $i),
                    ];
                }
            }

            return $ponPorts;
        } catch (Exception $e) {
            return [];
        }
    }

    protected function getOnuCountForPort(int $card, int $port): int
    {
        // Implementasi untuk mendapatkan jumlah ONU per port
        // Ini contoh, sesuaikan dengan OID ZTE C300
        try {
            $onuList = $this->getOnuList($card, $port);
            return count($onuList);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getOnuList(int $card, int $port): array
    {
        try {
            $this->connect();

            $onuList = [];
            
            // Walk through ONU table untuk port tertentu
            $onuTable = $this->snmpWalk(self::OID_ZTE_ONU_TABLE . ".{$card}.{$port}");

            if ($onuTable && is_array($onuTable)) {
                foreach ($onuTable as $oid => $onuIndex) {
                    // Parse OID untuk mendapatkan ONU index
                    if (preg_match('/\.(\d+)$/', $oid, $matches)) {
                        $onuIdx = (int) $matches[1];
                        
                        // Get serial number
                        $serialOid = self::OID_ZTE_ONU_SERIAL . ".{$card}.{$port}.{$onuIdx}";
                        $serial = $this->snmpGet($serialOid);
                        
                        // Get signal
                        $signalOid = self::OID_ZTE_ONU_SIGNAL . ".{$card}.{$port}.{$onuIdx}";
                        $signal = $this->snmpGet($signalOid);
                        
                        // Get status
                        $statusOid = self::OID_ZTE_ONU_STATUS . ".{$card}.{$port}.{$onuIdx}";
                        $status = $this->snmpGet($statusOid);
                        
                        if ($serial) {
                            $onuList[] = [
                                'onu_id' => $onuIdx,
                                'serial_number' => trim($serial, '"'),
                                'card' => $card,
                                'port' => $port,
                                'rx_power' => $signal ? (float) $signal : null,
                                'status' => $this->parseOnuStatus($status),
                            ];
                        }
                    }
                }
            }

            return $onuList;
        } catch (Exception $e) {
            Log::error("ZTE C300 getOnuList error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseOnuStatus($status): string
    {
        if (!$status) return 'unknown';
        
        $status = strtolower(trim($status, '"'));
        if (strpos($status, 'online') !== false || strpos($status, '1') !== false) {
            return 'online';
        }
        if (strpos($status, 'offline') !== false || strpos($status, '0') !== false) {
            return 'offline';
        }
        if (strpos($status, 'los') !== false) {
            return 'los';
        }
        return 'unknown';
    }

    public function getStatistics(): array
    {
        try {
            $this->connect();

            $uptime = $this->snmpGet(self::OID_SYSTEM_UPTIME);

            return [
                'uptime' => $uptime ?: '0',
                'last_check' => now()->toDateTimeString(),
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    public function getOnuDetails(string $serialNumber): array
    {
        try {
            $this->connect();
            
            // Search for ONU across all ports
            $ponPorts = $this->getPonPorts();
            foreach ($ponPorts as $port) {
                $onuList = $this->getOnuList($port['card'], $port['port']);
                foreach ($onuList as $onu) {
                    if ($onu['serial_number'] === $serialNumber) {
                        // Get additional details
                        $onuIdx = $onu['onu_id'];
                        $card = $onu['card'];
                        $port = $onu['port'];
                        
                        // Get bandwidth usage
                        $bandwidthOid = self::OID_ZTE_ONU_BANDWIDTH . ".{$card}.{$port}.{$onuIdx}";
                        $bandwidth = $this->snmpGet($bandwidthOid);
                        
                        return [
                            'serial_number' => $serialNumber,
                            'card' => $card,
                            'port' => $port,
                            'onu_id' => $onuIdx,
                            'rx_power' => $onu['rx_power'],
                            'tx_power' => null, // May need separate OID
                            'status' => $onu['status'],
                            'bandwidth' => $bandwidth ? $this->parseBandwidth($bandwidth) : null,
                            'last_seen' => now()->toDateTimeString(),
                        ];
                    }
                }
            }
            
            return [];
        } catch (Exception $e) {
            Log::error("ZTE C300 getOnuDetails error: " . $e->getMessage());
            return [];
        }
    }
    
    protected function parseBandwidth($data): array
    {
        // Parse bandwidth data from SNMP response
        // Format may vary, adjust based on actual OLT response
        return [
            'download' => 0,
            'upload' => 0,
        ];
    }

    public function getBandwidthUsage(?int $card = null, ?int $port = null): array
    {
        try {
            $this->connect();
            
            $bandwidthData = [];
            
            if ($card && $port) {
                // Get bandwidth for specific port
                $onuList = $this->getOnuList($card, $port);
                foreach ($onuList as $onu) {
                    $onuIdx = $onu['onu_id'];
                    $bandwidthOid = self::OID_ZTE_ONU_BANDWIDTH . ".{$card}.{$port}.{$onuIdx}";
                    $bandwidth = $this->snmpGet($bandwidthOid);
                    
                    if ($bandwidth) {
                        $bandwidthData[] = [
                            'serial_number' => $onu['serial_number'],
                            'card' => $card,
                            'port' => $port,
                            'bandwidth' => $this->parseBandwidth($bandwidth),
                        ];
                    }
                }
            } else {
                // Get bandwidth for all ports
                $ponPorts = $this->getPonPorts();
                foreach ($ponPorts as $portData) {
                    $portBandwidth = $this->getBandwidthUsage($portData['card'], $portData['port']);
                    $bandwidthData = array_merge($bandwidthData, $portBandwidth);
                }
            }
            
            return $bandwidthData;
        } catch (Exception $e) {
            Log::error("ZTE C300 getBandwidthUsage error: " . $e->getMessage());
            return [];
        }
    }

    public function discoverUnconfiguredOnus(): array
    {
        try {
            $this->connect();
            
            $unconfigured = [];
            $ponPorts = $this->getPonPorts();
            
            foreach ($ponPorts as $port) {
                // Walk through unconfigured ONU table
                $unconfiguredOid = '1.3.6.1.4.1.3902.1015.1.1.1.1.10'; // OID for unconfigured ONUs
                $onuTable = $this->snmpWalk($unconfiguredOid . ".{$port['card']}.{$port['port']}");
                
                if ($onuTable && is_array($onuTable)) {
                    foreach ($onuTable as $oid => $value) {
                        if (preg_match('/\.(\d+)$/', $oid, $matches)) {
                            $onuIdx = (int) $matches[1];
                            
                            // Get serial number
                            $serialOid = $unconfiguredOid . ".{$port['card']}.{$port['port']}.{$onuIdx}";
                            $serial = $this->snmpGet($serialOid);
                            
                            // Get signal
                            $signalOid = self::OID_ZTE_ONU_SIGNAL . ".{$port['card']}.{$port['port']}.{$onuIdx}";
                            $signal = $this->snmpGet($signalOid);
                            
                            if ($serial) {
                                $unconfigured[] = [
                                    'serial_number' => trim($serial, '"'),
                                    'card' => $port['card'],
                                    'port' => $port['port'],
                                    'signal' => $signal ? (float) $signal : null,
                                    'vendor' => null,
                                    'model' => null,
                                ];
                            }
                        }
                    }
                }
            }
            
            return $unconfigured;
        } catch (Exception $e) {
            Log::error("ZTE C300 discoverUnconfiguredOnus error: " . $e->getMessage());
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
            
            // Use write community for provisioning
            $writeCommunity = $this->olt->write_snmp_community ?? $this->olt->snmp_community ?? 'private';
            
            // SNMP SET untuk register ONU
            $registerOid = self::OID_ZTE_ONU_REGISTER . ".{$card}.{$port}";
            $result = $this->snmpSet($registerOid, 's', $serialNumber);
            
            if (!$result) {
                $lastError = error_get_last();
                $errorDetail = $lastError ? $lastError['message'] : 'Unknown SNMP error';
                
                // Check common SNMP errors
                if (strpos($errorDetail, 'timeout') !== false || strpos($errorDetail, 'No response') !== false) {
                    throw new Exception('Gagal register ONU: Timeout saat mengirim perintah ke OLT. Pastikan OLT dapat diakses dan write community string benar.');
                } elseif (strpos($errorDetail, 'authentication') !== false || strpos($errorDetail, 'community') !== false) {
                    throw new Exception('Gagal register ONU: Write community string salah atau tidak memiliki izin write. Pastikan menggunakan write community (biasanya "private") dan OLT dikonfigurasi dengan benar.');
                } elseif (strpos($errorDetail, 'No such instance') !== false || strpos($errorDetail, 'OID') !== false) {
                    throw new Exception('Gagal register ONU: OID tidak ditemukan di OLT. Mungkin firmware OLT berbeda atau OID perlu disesuaikan.');
                } else {
                    throw new Exception('Gagal register ONU via SNMP: ' . $errorDetail . '. Pastikan write community string benar dan OLT dapat diakses.');
                }
            }
            
            // Wait a bit for ONU to register
            sleep(2);
            
            // Get ONU ID after registration
            $onuId = $this->getOnuIdAfterRegistration($card, $port, $serialNumber);
            
            return [
                'success' => true,
                'message' => 'ONU berhasil diregistrasi',
                'onu_id' => $onuId,
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 provisionOnu error: " . $e->getMessage());
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
            
            $rebootOid = self::OID_ZTE_ONU_REBOOT . ".{$onuData['card']}.{$onuData['port']}.{$onuData['onu_id']}";
            $result = $this->snmpSet($rebootOid, 'i', 1); // reboot command
            
            if ($result === false) {
                throw new Exception('Gagal reboot ONU. Pastikan write community string benar.');
            }
            
            return [
                'success' => true,
                'message' => 'Perintah reboot berhasil dikirim',
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 rebootOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function resetOnu(string $serialNumber): array
    {
        // Reset biasanya sama dengan reboot untuk ZTE
        return $this->rebootOnu($serialNumber);
    }

    public function disableOnu(string $serialNumber): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($serialNumber);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan');
            }
            
            $disableOid = self::OID_ZTE_ONU_DISABLE . ".{$onuData['card']}.{$onuData['port']}.{$onuData['onu_id']}";
            $result = $this->snmpSet($disableOid, 'i', 1); // disable command
            
            if ($result === false) {
                throw new Exception('Gagal disable ONU. Pastikan write community string benar.');
            }
            
            return [
                'success' => true,
                'message' => 'ONU berhasil di-disable',
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 disableOnu error: " . $e->getMessage());
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
            
            $enableOid = self::OID_ZTE_ONU_ENABLE . ".{$onuData['card']}.{$onuData['port']}.{$onuData['onu_id']}";
            $result = $this->snmpSet($enableOid, 'i', 0); // enable command (0 = enable)
            
            if ($result === false) {
                throw new Exception('Gagal enable ONU. Pastikan write community string benar.');
            }
            
            return [
                'success' => true,
                'message' => 'ONU berhasil di-enable',
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 enableOnu error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getOnuConfig(string $serialNumber): array
    {
        try {
            $details = $this->getOnuDetails($serialNumber);
            if (empty($details)) {
                return [];
            }
            
            // Get service configuration
            // This may require additional OIDs for VLAN, PPPoE, etc.
            return [
                'serial_number' => $serialNumber,
                'card' => $details['card'],
                'port' => $details['port'],
                'onu_id' => $details['onu_id'],
                'status' => $details['status'],
                'rx_power' => $details['rx_power'],
                'config' => [
                    // Additional config OIDs can be added here
                ],
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 getOnuConfig error: " . $e->getMessage());
            return [];
        }
    }

    public function configureService(\App\Models\Onu $onu, \App\Models\OnuService $service, array $payload = []): array
    {
        try {
            $this->connect();
            
            $onuData = $this->findOnuBySerial($onu->serial_number);
            if (!$onuData) {
                throw new Exception('ONU tidak ditemukan di OLT');
            }
            
            $writeCommunity = $this->olt->write_snmp_community ?? $this->olt->snmp_community ?? 'private';
            
            // Configure VLAN
            if ($service->vlan_id) {
                $vlanOid = '1.3.6.1.4.1.3902.1015.1.1.1.1.11'; // OID for VLAN config
                $vlanConfigOid = $vlanOid . ".{$onuData['card']}.{$onuData['port']}.{$onuData['onu_id']}";
                
                $result = $this->snmpSet($vlanConfigOid, 'i', $service->vlan_id);
                
                if ($result === false) {
                    throw new Exception('Gagal configure VLAN');
                }
            }
            
            // Configure PPPoE (if applicable)
            if ($service->wan_mode === 'pppoe' && $service->pppoe_username) {
                // PPPoE configuration OIDs
                // This requires specific OIDs from ZTE MIB
                // Placeholder for now
            }
            
            return [
                'success' => true,
                'message' => 'Service berhasil dikonfigurasi',
            ];
        } catch (Exception $e) {
            Log::error("ZTE C300 configureService error: " . $e->getMessage());
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

