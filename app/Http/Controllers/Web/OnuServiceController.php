<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Onu;
use App\Models\OnuService;
use App\Models\SpeedProfile;
use App\Models\VlanDatabase;
use Illuminate\Http\Request;
use App\Jobs\ApplyOnuServiceConfigJob;

class OnuServiceController extends BaseController
{
    /**
     * Store a newly created service.
     */
    public function store(Request $request, Onu $onu)
    {
        $request->validate([
            'service_id' => 'required|integer|min:1|max:4',
            'wan_mode' => 'required|in:pppoe,dhcp,static,bridge',
            'vlan_id' => 'nullable|integer|min:1|max:4096',
            'vlan_id_manual' => 'nullable|integer|min:1|max:4096',
            'pppoe_username' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
            'pppoe_password' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
            'static_ip' => 'nullable|ip|required_if:wan_mode,static',
            'static_gateway' => 'nullable|ip|required_if:wan_mode,static',
            'static_subnet' => 'nullable|string|max:255|required_if:wan_mode,static',
            'static_dns1' => 'nullable|ip',
            'static_dns2' => 'nullable|ip',
            'speed_profile_id' => 'nullable|exists:speed_profiles,id',
            'is_active' => 'nullable|boolean',
            'wifi' => 'nullable|array',
            'wifi.enabled' => 'nullable|boolean',
            'wifi.ssid' => 'nullable|string|max:255',
            'wifi.password' => 'nullable|string|max:255',
            'wifi.security' => 'nullable|string|max:50',
            'wifi.band' => 'nullable|string|max:50',
            'wifi.channel' => 'nullable|integer|min:1|max:165',
            'lan_ports' => 'nullable|array',
            'lan_ports.*.mode' => 'nullable|string|max:50',
            'lan_ports.*.vlan' => 'nullable|integer|min:1|max:4096',
        ]);

        // Check if service_id already exists
        $existingService = OnuService::where('onu_id', $onu->id)
            ->where('service_id', $request->service_id)
            ->first();

        if ($existingService) {
            return back()->withInput()->with('error', "Service {$request->service_id} sudah ada. Gunakan edit untuk mengubah.");
        }

        $vlanId = !empty($request->vlan_id_manual) ? $request->vlan_id_manual : ($request->vlan_id ?? null);
        $speedProfile = $request->speed_profile_id ? SpeedProfile::find($request->speed_profile_id) : null;
        $wifiConfig = $this->sanitizeWifiConfig($request->input('wifi', []));
        $lanPortConfig = $this->sanitizeLanPortConfig($request->input('lan_ports', []));

        $service = OnuService::create([
            'onu_id' => $onu->id,
            'service_id' => $request->service_id,
            'wan_mode' => $request->wan_mode,
            'pppoe_username' => $request->pppoe_username ?? null,
            'pppoe_password' => $request->pppoe_password ?? null,
            'static_ip' => $request->static_ip ?? null,
            'static_gateway' => $request->static_gateway ?? null,
            'static_subnet' => $request->static_subnet ?? null,
            'static_dns1' => $request->static_dns1 ?? null,
            'static_dns2' => $request->static_dns2 ?? null,
            'vlan_id' => $vlanId,
            'vlan_tagged' => true,
            'speed_profile_id' => $request->speed_profile_id ?? null,
            'download_speed' => $speedProfile?->download_speed ?? null,
            'upload_speed' => $speedProfile?->upload_speed ?? null,
            'is_active' => $request->is_active ?? true,
            'wifi_config' => $wifiConfig,
            'lan_port_config' => $lanPortConfig,
        ]);

        ApplyOnuServiceConfigJob::dispatch($service->id);

        return back()->with('success', "Service {$request->service_id} berhasil ditambahkan.");
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Onu $onu, OnuService $service)
    {
        // Ensure service belongs to ONU
        if ($service->onu_id !== $onu->id) {
            return back()->with('error', 'Service tidak ditemukan.');
        }

        $request->validate([
            'wan_mode' => 'required|in:pppoe,dhcp,static,bridge',
            'vlan_id' => 'nullable|integer|min:1|max:4096',
            'vlan_id_manual' => 'nullable|integer|min:1|max:4096',
            'pppoe_username' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
            'pppoe_password' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
            'static_ip' => 'nullable|ip|required_if:wan_mode,static',
            'static_gateway' => 'nullable|ip|required_if:wan_mode,static',
            'static_subnet' => 'nullable|string|max:255|required_if:wan_mode,static',
            'static_dns1' => 'nullable|ip',
            'static_dns2' => 'nullable|ip',
            'speed_profile_id' => 'nullable|exists:speed_profiles,id',
            'is_active' => 'nullable|boolean',
            'wifi' => 'nullable|array',
            'wifi.enabled' => 'nullable|boolean',
            'wifi.ssid' => 'nullable|string|max:255',
            'wifi.password' => 'nullable|string|max:255',
            'wifi.security' => 'nullable|string|max:50',
            'wifi.band' => 'nullable|string|max:50',
            'wifi.channel' => 'nullable|integer|min:1|max:165',
            'lan_ports' => 'nullable|array',
            'lan_ports.*.mode' => 'nullable|string|max:50',
            'lan_ports.*.vlan' => 'nullable|integer|min:1|max:4096',
        ]);

        $vlanId = !empty($request->vlan_id_manual) ? $request->vlan_id_manual : ($request->vlan_id ?? null);
        $speedProfile = $request->speed_profile_id ? SpeedProfile::find($request->speed_profile_id) : null;
        $wifiConfig = $this->sanitizeWifiConfig($request->input('wifi', []));
        $lanPortConfig = $this->sanitizeLanPortConfig($request->input('lan_ports', []));

        $service->update([
            'wan_mode' => $request->wan_mode,
            'pppoe_username' => $request->pppoe_username ?? null,
            'pppoe_password' => $request->pppoe_password ?? null,
            'static_ip' => $request->static_ip ?? null,
            'static_gateway' => $request->static_gateway ?? null,
            'static_subnet' => $request->static_subnet ?? null,
            'static_dns1' => $request->static_dns1 ?? null,
            'static_dns2' => $request->static_dns2 ?? null,
            'vlan_id' => $vlanId,
            'speed_profile_id' => $request->speed_profile_id ?? null,
            'download_speed' => $speedProfile?->download_speed ?? null,
            'upload_speed' => $speedProfile?->upload_speed ?? null,
            'is_active' => $request->is_active ?? true,
            'wifi_config' => $wifiConfig,
            'lan_port_config' => $lanPortConfig,
        ]);

        ApplyOnuServiceConfigJob::dispatch($service->id);

        return back()->with('success', "Service {$service->service_id} berhasil diperbarui.");
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Request $request, Onu $onu, OnuService $service)
    {
        // Ensure service belongs to ONU
        if ($service->onu_id !== $onu->id) {
            return back()->with('error', 'Service tidak ditemukan.');
        }

        $serviceId = $service->service_id;
        $service->delete();

        return back()->with('success', "Service {$serviceId} berhasil dihapus.");
    }

    /**
     * Update remote access rules
     */
    public function updateRemoteAccess(Request $request, Onu $onu, OnuService $service)
    {
        // Ensure service belongs to ONU
        if ($service->onu_id !== $onu->id) {
            return back()->with('error', 'Service tidak ditemukan.');
        }

        // Handle JSON string from form
        $rules = $request->input('rules');
        
        \Log::info('Remote access rules update attempt', [
            'onu_id' => $onu->id,
            'service_id' => $service->id,
            'rules_type' => gettype($rules),
            'rules_value' => is_string($rules) ? substr($rules, 0, 200) : $rules,
        ]);
        
        // If it's a JSON string, decode it
        if (is_string($rules)) {
            $decoded = json_decode($rules, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rules = $decoded;
            } else {
                \Log::error('Invalid JSON in remote access rules', [
                    'onu_id' => $onu->id,
                    'service_id' => $service->id,
                    'rules_string' => $rules,
                    'json_error' => json_last_error_msg(),
                ]);
                return back()->withInput()->with('error', 'Format rules tidak valid: ' . json_last_error_msg());
            }
        }

        // Validate rules
        if (empty($rules) || !is_array($rules)) {
            \Log::error('Empty or invalid rules array', [
                'onu_id' => $onu->id,
                'service_id' => $service->id,
                'rules' => $rules,
            ]);
            return back()->withInput()->with('error', 'Rules tidak boleh kosong. Pastikan Anda sudah menambahkan rule ke list sebelum menyimpan.');
        }

        foreach ($rules as $index => $rule) {
            if (empty($rule['name'])) {
                return back()->withInput()->with('error', "Rule #{$index}: Nama rule wajib diisi.");
            }
            if (empty($rule['ingress'])) {
                return back()->withInput()->with('error', "Rule #{$index}: Ingress wajib diisi.");
            }
            if (empty($rule['service'])) {
                return back()->withInput()->with('error', "Rule #{$index}: Service wajib diisi.");
            }
            if (empty($rule['action']) || !in_array($rule['action'], ['allow', 'deny'])) {
                return back()->withInput()->with('error', "Rule #{$index}: Action harus allow atau deny.");
            }
        }

        try {
            $service->update([
                'remote_access_rules' => $rules,
            ]);

            ApplyOnuServiceConfigJob::dispatch($service->id);

            return back()->with('success', 'Remote access rules berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Failed to update remote access rules', [
                'onu_id' => $onu->id,
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'rules' => $rules,
            ]);
            
            return back()->withInput()->with('error', 'Gagal menyimpan rules: ' . $e->getMessage());
        }
    }

    /**
     * Sanitize WiFi config input
     */
    protected function sanitizeWifiConfig(array $wifi): ?array
    {
        if (empty($wifi)) {
            return null;
        }

        $wifi['enabled'] = filter_var($wifi['enabled'] ?? false, FILTER_VALIDATE_BOOL);
        $wifi['ssid'] = $wifi['ssid'] ?? null;
        $wifi['password'] = $wifi['password'] ?? null;
        $wifi['security'] = $wifi['security'] ?? null;
        $wifi['band'] = $wifi['band'] ?? null;
        $wifi['channel'] = $wifi['channel'] ?? null;

        $filtered = array_filter($wifi, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return empty($filtered) ? null : $filtered;
    }

    /**
     * Sanitize LAN port config input
     */
    protected function sanitizeLanPortConfig(array $lanPorts): ?array
    {
        if (empty($lanPorts)) {
            return null;
        }

        $cleaned = [];
        foreach ($lanPorts as $port => $config) {
            if (empty($config)) {
                continue;
            }

            $entry = [
                'mode' => $config['mode'] ?? null,
                'vlan' => $config['vlan'] ?? null,
                'description' => $config['description'] ?? null,
            ];

            $entry = array_filter($entry, function ($value) {
                return !is_null($value) && $value !== '';
            });

            if (!empty($entry)) {
                $cleaned[$port] = $entry;
            }
        }

        return empty($cleaned) ? null : $cleaned;
    }
}

