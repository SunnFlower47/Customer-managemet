<?php

namespace App\Services;

use App\Models\Mikrotik;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class MikrotikService
{
    /**
     * Check if testing mode is enabled
     */
    private function isTestingMode(): bool
    {
        return Config::get('mikrotik.testing_mode', false);
    }

    /**
     * Test connection to MikroTik router
     */
    public function testConnection(Mikrotik $mikrotik): array
    {
        // Return mock data if testing mode is enabled
        if ($this->isTestingMode()) {
            $mikrotik->update([
                'connection_status' => 'online',
                'last_connected_at' => now(),
                'last_error' => null,
            ]);

            return [
                'success' => true,
                'message' => 'Koneksi berhasil (Testing Mode)',
                'identity' => Config::get('mikrotik.mock.identity', 'Test Router'),
            ];
        }

        try {
            $connection = $this->connect($mikrotik);

            if ($connection) {
                // Try to get system identity
                $identity = $this->query($connection, '/system/identity/print');

                $this->disconnect($connection);

                $mikrotik->update([
                    'connection_status' => 'online',
                    'last_connected_at' => now(),
                    'last_error' => null,
                ]);

                return [
                    'success' => true,
                    'message' => 'Koneksi berhasil',
                    'identity' => $identity[0]['name'] ?? 'Unknown',
                ];
            }
        } catch (Exception $e) {
            $mikrotik->update([
                'connection_status' => 'error',
                'last_error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal membuat koneksi',
        ];
    }

    /**
     * Connect to MikroTik router
     */
    public function connect(Mikrotik $mikrotik)
    {
        $ip = $mikrotik->ip_address;
        $port = $mikrotik->port;
        $username = $mikrotik->username;
        $password = decrypt($mikrotik->password);

        // Use RouterOS API
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            throw new Exception('Gagal membuat socket');
        }

        $connected = @socket_connect($socket, $ip, $port);

        if (!$connected) {
            socket_close($socket);
            throw new Exception('Gagal terhubung ke ' . $ip . ':' . $port);
        }

        // Login to RouterOS API
        $this->login($socket, $username, $password);

        return $socket;
    }

    /**
     * Login to RouterOS API
     */
    private function login($socket, $username, $password)
    {
        // Send login command
        $login = '/login';
        $this->write($socket, $login);
        $response = $this->read($socket);

        if (isset($response['!trap'])) {
            throw new Exception('Login gagal: ' . ($response['!trap'][0]['message'] ?? 'Unknown error'));
        }

        // Send username
        $this->write($socket, '=name=' . $username);
        $response = $this->read($socket);

        // Send password (with challenge if needed)
        if (isset($response['ret'])) {
            $challenge = $response['ret'];
            $hashedPassword = md5(chr(0) . $password . pack('H*', $challenge));
            $this->write($socket, '=response=00' . $hashedPassword);
        } else {
            $this->write($socket, '=password=' . $password);
        }

        $response = $this->read($socket);

        if (isset($response['!trap'])) {
            throw new Exception('Login gagal: ' . ($response['!trap'][0]['message'] ?? 'Invalid credentials'));
        }
    }

    /**
     * Query RouterOS API
     */
    public function query($socket, $command, $params = [])
    {
        $this->write($socket, $command);

        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $this->write($socket, $value);
            } else {
                $this->write($socket, '=' . $key . '=' . $value);
            }
        }

        $this->write($socket, '');

        return $this->read($socket);
    }

    /**
     * Write to socket
     */
    private function write($socket, $command)
    {
        $length = strlen($command);
        $lengthBytes = pack('N', $length);
        socket_write($socket, $lengthBytes . $command);
    }

    /**
     * Read from socket
     */
    private function read($socket)
    {
        $response = [];

        while (true) {
            $lengthBytes = socket_read($socket, 4);

            if ($lengthBytes === false || strlen($lengthBytes) < 4) {
                break;
            }

            $length = unpack('N', $lengthBytes)[1];

            if ($length == 0) {
                break;
            }

            $data = socket_read($socket, $length);

            if ($data === false) {
                break;
            }

            if (strpos($data, '=') !== false) {
                $parts = explode('=', $data, 2);
                $key = $parts[0];
                $value = isset($parts[1]) ? $parts[1] : '';

                if (!isset($response[$key])) {
                    $response[$key] = $value;
                } else {
                    if (!is_array($response[$key])) {
                        $response[$key] = [$response[$key]];
                    }
                    $response[$key][] = $value;
                }
            } else {
                if (!isset($response['!done'])) {
                    $response['!done'] = [];
                }
                if (!isset($response['!trap'])) {
                    $response['!trap'] = [];
                }

                if ($data == '!done') {
                    $response['!done'][] = [];
                } elseif ($data == '!trap') {
                    $response['!trap'][] = [];
                } elseif ($data == '!re') {
                    $response['!re'][] = [];
                }
            }
        }

        return $response;
    }

    /**
     * Disconnect from router
     */
    public function disconnect($socket)
    {
        if ($socket) {
            @socket_close($socket);
        }
    }

    /**
     * Find PPPoE user in router
     */
    public function findPppoe(Mikrotik $mikrotik, string $username): ?array
    {
        // Return mock data if testing mode is enabled
        if ($this->isTestingMode()) {
            $mockSecrets = Config::get('mikrotik.mock.pppoe_secrets', []);
            return $mockSecrets[$username] ?? null;
        }

        try {
            $connection = $this->connect($mikrotik);

            $pppoeUsers = $this->query($connection, '/ppp/secret/print', ['?name=' . $username]);

            $this->disconnect($connection);

            if (isset($pppoeUsers['!re']) && count($pppoeUsers['!re']) > 0) {
                return $pppoeUsers['!re'][0];
            }

            return null;
        } catch (Exception $e) {
            Log::error('Error finding PPPoE: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all active PPPoE users
     */
    public function getActivePppoeUsers(Mikrotik $mikrotik): array
    {
        // Return mock data if testing mode is enabled
        if ($this->isTestingMode()) {
            return Config::get('mikrotik.mock.pppoe_users', []);
        }

        try {
            $connection = $this->connect($mikrotik);

            $pppoeUsers = $this->query($connection, '/ppp/active/print');

            $this->disconnect($connection);

            if (isset($pppoeUsers['!re'])) {
                return $pppoeUsers['!re'];
            }

            return [];
        } catch (Exception $e) {
            Log::error('Error getting active PPPoE users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get router resource usage
     */
    public function getResourceUsage(Mikrotik $mikrotik): array
    {
        // Return mock data if testing mode is enabled
        if ($this->isTestingMode()) {
            return Config::get('mikrotik.mock.resource_usage', []);
        }

        try {
            $connection = $this->connect($mikrotik);

            $resources = $this->query($connection, '/system/resource/print');

            $this->disconnect($connection);

            if (isset($resources['!re']) && count($resources['!re']) > 0) {
                return $resources['!re'][0];
            }

            return [];
        } catch (Exception $e) {
            Log::error('Error getting resource usage: ' . $e->getMessage());
            return [];
        }
    }
}

