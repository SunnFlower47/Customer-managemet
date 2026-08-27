<?php

namespace App\Services;

use App\Models\Mikrotik;
use App\Models\MikrotikPppoeUser;
use App\Models\Pelanggan;
use Exception;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $router;
    protected $socket;
    protected $connected = false;
    protected $debug = false;
    protected $error_no;
    protected $error_str;

    /**
     * Connect to MikroTik Router
     */
    public function connect(Mikrotik $router)
    {
        $this->router = $router;
        $this->socket = @fsockopen($router->ip_address, $router->port ?? 8728, $this->error_no, $this->error_str, 5);

        if (!$this->socket) {
            throw new Exception("Connection failed: {$this->error_str} ({$this->error_no})");
        }

        // Login process
        if (!$this->login($router->username, $router->decrypted_password)) {
            fclose($this->socket);
            throw new Exception("Authentication failed for user: {$router->username}");
        }

        $this->connected = true;
        
        // Update status
        $router->update([
            'connection_status' => 'online',
            'last_connected_at' => now(),
            'last_error' => null
        ]);

        return $this;
    }

    /**
     * Test Connection wrapper
     */
    public function testConnection(Mikrotik $router): array
    {
        try {
            $this->connect($router);
            $this->disconnect();
            
            return [
                'success' => true,
                'message' => 'Connection successful!'
            ];
        } catch (Exception $e) {
            $router->update([
                'connection_status' => 'error',
                'last_error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync PPPoE Users and Active Sessions
     */
    public function syncPppoeUsers(Mikrotik $router): array
    {
        try {
            if (!$this->connected) {
                $this->connect($router);
            }

            // Get Secrets (Users)
            $secrets = $this->comm('/ppp/secret/print');
            
            // Get Active Connections
            $activeConnections = $this->comm('/ppp/active/print');
            $activeMap = [];
            foreach ($activeConnections as $active) {
                if (isset($active['name'])) {
                    $activeMap[$active['name']] = $active;
                }
            }

            // Get PPP Profiles (for users that inherit remote-address from profile)
            $profiles = $this->comm('/ppp/profile/print');
            $profileMap = [];
            foreach ($profiles as $profile) {
                if (!empty($profile['name'])) {
                    $profileMap[$profile['name']] = $profile;
                }
            }

            $syncedCount = 0;
            $newCount = 0;
            $updatedCount = 0;

            // Mark all existing as inactive first (optional strategy, or just update synced_at)
            // For now, we'll just update/create
            
            foreach ($secrets as $secret) {
                // Skip if name is empty
                if (empty($secret['name'])) continue;

                $username = $secret['name'];
                $isActive = isset($activeMap[$username]);
                $lastSeen = $isActive ? now() : null;
                $macAddress = $isActive ? ($activeMap[$username]['caller-id'] ?? null) : null;
                $profileName = $secret['profile'] ?? 'default';
                $profileRemoteAddress = $profileMap[$profileName]['remote-address'] ?? null;

                // Priority:
                // 1) Active session IP (actual current assigned IP)
                // 2) Secret remote-address (user static)
                // 3) Profile remote-address (can be static IP or pool name)
                $remoteAddress = $activeMap[$username]['address']
                    ?? ($secret['remote-address'] ?? $profileRemoteAddress);
                $status = ($secret['disabled'] ?? 'false') === 'true' ? 'disabled' : 'enabled';
                
                // Find or Create PPPoE User Record
                $pppoeUser = MikrotikPppoeUser::updateOrCreate(
                    [
                        'mikrotik_id' => $router->id,
                        'username' => $username,
                    ],
                    [
                        'password' => $secret['password'] ?? null, // Note: active/print doesn't show pw, secret/print might
                        'service' => $secret['service'] ?? 'pppoe',
                        'profile' => $profileName,
                        'local_address' => $secret['local-address'] ?? null,
                        'remote_address' => $remoteAddress,
                        'mac_address' => $macAddress,
                        'status' => $status,
                        'is_active' => $isActive,
                        'last_seen' => $lastSeen,
                    ]
                );

                // Auto-Match with Pelanggan
                if (!$pppoeUser->pelanggan_id) {
                    $pelanggan = Pelanggan::where('pppoe', $username)->first();
                    if ($pelanggan) {
                        $pppoeUser->update(['pelanggan_id' => $pelanggan->id]);
                        $pelanggan->update([
                            'mikrotik_id' => $router->id,
                            'exists_in_mikrotik' => true,
                            'mikrotik_last_checked' => now(),
                            'mikrotik_router_name' => $router->nama,
                            'mikrotik_status' => $status,
                            'mikrotik_ip' => $remoteAddress,
                            'mikrotik_profile' => $profileName,
                        ]);
                    }
                } else {
                    $pppoeUser->pelanggan?->update([
                        'mikrotik_id' => $router->id,
                        'exists_in_mikrotik' => true,
                        'mikrotik_last_checked' => now(),
                        'mikrotik_router_name' => $router->nama,
                        'mikrotik_status' => $status,
                        'mikrotik_ip' => $remoteAddress,
                        'mikrotik_profile' => $profileName,
                    ]);
                }

                if ($pppoeUser->wasRecentlyCreated) {
                    $newCount++;
                } else {
                    $updatedCount++;
                }
                $syncedCount++;
            }

            return [
                'success' => true,
                'total' => $syncedCount,
                'new' => $newCount,
                'updated' => $updatedCount
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Isolate / Disable PPPoE User on MikroTik
     */
    public function isolateUser(Mikrotik $router, string $username, ?string $isolateProfile = null): array
    {
        try {
            if (!$this->connected) {
                $this->connect($router);
            }

            // Find secret
            $secrets = $this->comm('/ppp/secret/print', ['?name' => $username]);
            if (empty($secrets)) {
                return ['success' => false, 'message' => "User PPPoE '{$username}' tidak ditemukan di MikroTik"];
            }

            $secretId = $secrets[0]['.id'];

            if ($isolateProfile) {
                // Change profile to isolate profile
                $this->comm('/ppp/secret/set', [
                    '.id' => $secretId,
                    'profile' => $isolateProfile,
                    'disabled' => 'no'
                ]);
            } else {
                // Or disable secret
                $this->comm('/ppp/secret/set', [
                    '.id' => $secretId,
                    'disabled' => 'yes'
                ]);
            }

            // Kick active session so new policy applies immediately
            $this->kickActiveSession($router, $username);

            // Update local records
            MikrotikPppoeUser::where('mikrotik_id', $router->id)
                ->where('username', $username)
                ->update([
                    'status' => $isolateProfile ? 'isolated' : 'disabled',
                    'is_active' => false
                ]);

            return ['success' => true, 'message' => "Pelanggan '{$username}' berhasil diisolir pada MikroTik."];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Unisolate / Enable PPPoE User on MikroTik
     */
    public function unisolateUser(Mikrotik $router, string $username, ?string $normalProfile = null): array
    {
        try {
            if (!$this->connected) {
                $this->connect($router);
            }

            $secrets = $this->comm('/ppp/secret/print', ['?name' => $username]);
            if (empty($secrets)) {
                return ['success' => false, 'message' => "User PPPoE '{$username}' tidak ditemukan di MikroTik"];
            }

            $secretId = $secrets[0]['.id'];
            $params = [
                '.id' => $secretId,
                'disabled' => 'no'
            ];

            if ($normalProfile) {
                $params['profile'] = $normalProfile;
            }

            $this->comm('/ppp/secret/set', $params);

            // Update local records
            MikrotikPppoeUser::where('mikrotik_id', $router->id)
                ->where('username', $username)
                ->update(['status' => 'enabled']);

            return ['success' => true, 'message' => "Pelanggan '{$username}' berhasil diaktifkan kembali pada MikroTik."];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Kick active PPPoE session
     */
    public function kickActiveSession(Mikrotik $router, string $username): bool
    {
        try {
            if (!$this->connected) {
                $this->connect($router);
            }

            $actives = $this->comm('/ppp/active/print', ['?name' => $username]);
            foreach ($actives as $active) {
                if (isset($active['.id'])) {
                    $this->comm('/ppp/active/remove', ['.id' => $active['.id']]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    }

    public function disconnect()
    {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }
        $this->connected = false;
    }

    // --- Core RouterOS API Logic (Native PHP) ---

    protected function login($username, $password)
    {
        // RouterOS 6.43+ / v7: plain login
        $this->write('/login', [
            'name' => $username,
            'password' => $password,
        ]);
        $response = $this->read();

        $hasTrap = false;
        foreach ($response as $row) {
            if (isset($row['!trap'])) {
                $hasTrap = true;
                break;
            }
        }

        if (!$hasTrap) {
            return true;
        }

        // Fallback for old RouterOS challenge flow
        $this->write('/login');
        $challenge = $this->read();
        $token = null;

        foreach ($challenge as $row) {
            if (isset($row['ret'])) {
                $token = $row['ret'];
                break;
            }
        }

        if (!$token) {
            return false;
        }

        $passwordHash = md5(chr(0) . $password . pack('H*', $token));
        $this->write('/login', [
            'name' => $username,
            'response' => '00' . $passwordHash,
        ]);

        $response = $this->read();
        foreach ($response as $row) {
            if (isset($row['!trap'])) {
                return false;
            }
        }

        return true;
    }
    
    // Simplistic write
    protected function write($command, $param2 = true)
    {
        if ($command) {
            $this->sendWord($command);
        }
        if (is_array($param2)) {
            foreach ($param2 as $k => $v) {
                $this->sendWord('=' . $k . '=' . $v);
            }
            $this->sendWord('');
        } elseif ($param2 === true) {
             $this->sendWord('');
        }
    }

    protected function sendWord($word)
    {
        $len = strlen($word);

        if ($len < 0x80) {
            $prefix = chr($len);
        } elseif ($len < 0x4000) {
            $prefix = chr(($len >> 8) | 0x80) . chr($len & 0xFF);
        } elseif ($len < 0x200000) {
            $prefix = chr(($len >> 16) | 0xC0) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
        } elseif ($len < 0x10000000) {
            $prefix = chr(($len >> 24) | 0xE0) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
        } else {
            $prefix = chr(0xF0) . chr(($len >> 24) & 0xFF) . chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF);
        }

        fwrite($this->socket, $prefix);
        fwrite($this->socket, $word);
    }

    protected function read()
    {
        $response = [];
        $current = null;

        while (true) {
            $line = $this->readWord();
            if ($line === null) {
                break;
            }

            if ($line === '!re') {
                if (is_array($current) && !empty($current)) {
                    $response[] = $current;
                }
                $current = [];
                continue;
            }

            if ($line === '!done') {
                if (is_array($current) && !empty($current)) {
                    $response[] = $current;
                }
                break;
            }

            if ($line === '!trap') {
                $current = $current ?? [];
                $current['!trap'] = true;
                continue;
            }

            if (preg_match('/^=([^=]+)=(.*)$/', $line, $matches)) {
                $current = $current ?? [];
                $current[$matches[1]] = $matches[2];
            }
        }

        return $response;
    }

    protected function readWord()
    {
        if (feof($this->socket)) {
            return null;
        }

        $first = fread($this->socket, 1);
        if ($first === '' || $first === false) {
            return null;
        }

        $byte = ord($first);
        $length = 0;

        $readBytes = function ($num) {
            $data = '';
            while (strlen($data) < $num) {
                if (feof($this->socket)) {
                    break;
                }
                $chunk = fread($this->socket, $num - strlen($data));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $data .= $chunk;
            }
            return $data;
        };

        if (($byte & 0x80) === 0x00) {
            $length = $byte;
        } elseif (($byte & 0xC0) === 0x80) {
            $next = $readBytes(1);
            if (strlen($next) < 1) return null;
            $length = (($byte & 0x3F) << 8) + ord($next[0]);
        } elseif (($byte & 0xE0) === 0xC0) {
            $next = $readBytes(2);
            if (strlen($next) < 2) return null;
            $length = (($byte & 0x1F) << 16) + (ord($next[0]) << 8) + ord($next[1]);
        } elseif (($byte & 0xF0) === 0xE0) {
            $next = $readBytes(3);
            if (strlen($next) < 3) return null;
            $length = (($byte & 0x0F) << 24) + (ord($next[0]) << 16) + (ord($next[1]) << 8) + ord($next[2]);
        } elseif (($byte & 0xF8) === 0xF0) {
            $next = $readBytes(4);
            if (strlen($next) < 4) return null;
            $length = (ord($next[0]) << 24) + (ord($next[1]) << 16) + (ord($next[2]) << 8) + ord($next[3]);
        }

        if ($length === 0) {
            return '';
        }

        return $readBytes($length);
    }
    
    public function comm($comm, $arr = [])
    {
        $this->write($comm, $arr);
        $read = $this->read();
        return $read;
    }
}
