<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Olt;
use App\Models\Odp;
use App\Models\Pelanggan;
use App\Models\SpeedProfile;
use App\Models\VlanDatabase;
use App\Services\OnuProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnuRegisterController extends BaseController
{
    protected $onuProvisioning;

    public function __construct(OnuProvisioningService $onuProvisioning)
    {
        $this->onuProvisioning = $onuProvisioning;
    }

    /**
     * Show the form for creating a new ONU.
     */
    public function create(Request $request)
    {
        $olts = Olt::active()->get();
        $odps = Odp::active()->get();
        // Include pelanggans with status 'aktif' or 'bayar double' (both are active)
        $pelanggans = Pelanggan::whereIn('status', ['aktif', 'bayar double'])
            ->select('id', 'nama', 'pppoe', 'no_hp', 'odp_id')
            ->get();
        $speedProfiles = SpeedProfile::active()->get();
        $vlans = VlanDatabase::active()->orderBy('vlan_id')->get();
        $unconfiguredOnus = $this->onuProvisioning->getUnconfiguredOnus();
        $prefill = $request->only([
            'olt_id',
            'serial_number',
            'mac_address',
            'nama',
            'card',
            'port',
            'vendor',
            'model',
        ]);

        return view('onus.register', compact(
            'olts',
            'odps',
            'pelanggans',
            'speedProfiles',
            'vlans',
            'unconfiguredOnus',
            'prefill'
        ));
    }

    /**
     * Store a newly created ONU.
     */
    public function store(Request $request)
    {
        $messages = [
            'olt_id.required' => 'OLT wajib dipilih.',
            'olt_id.exists' => 'OLT yang dipilih tidak ditemukan.',
            'serial_number.required' => 'Serial Number ONU wajib diisi.',
            'serial_number.unique' => 'Serial Number ini sudah terdaftar. ONU mungkin sudah diregistrasi sebelumnya.',
            'serial_number.max' => 'Serial Number maksimal 255 karakter.',
            'card.integer' => 'Card harus berupa angka.',
            'card.min' => 'Card minimal 1.',
            'port.integer' => 'Port harus berupa angka.',
            'port.min' => 'Port minimal 1.',
            'pelanggan_id.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'odp_id.exists' => 'ODP yang dipilih tidak ditemukan.',
            'vlan_id.integer' => 'VLAN ID harus berupa angka.',
            'vlan_id.min' => 'VLAN ID minimal 1.',
            'vlan_id.max' => 'VLAN ID maksimal 4096.',
            'vlan_id_manual.integer' => 'VLAN ID manual harus berupa angka.',
            'vlan_id_manual.min' => 'VLAN ID manual minimal 1.',
            'vlan_id_manual.max' => 'VLAN ID manual maksimal 4096.',
            'wan_mode.in' => 'Mode WAN tidak valid. Pilih: PPPoE, DHCP, Static IP, atau Bridge.',
            'pppoe_username.required_if' => 'PPPoE Username wajib diisi jika menggunakan mode PPPoE.',
            'pppoe_password.required_if' => 'PPPoE Password wajib diisi jika menggunakan mode PPPoE.',
            'static_ip.required_if' => 'IP Address wajib diisi jika menggunakan mode Static IP.',
            'static_ip.ip' => 'Format IP Address tidak valid. Gunakan format IPv4 (contoh: 192.168.1.100).',
            'static_gateway.required_if' => 'Gateway wajib diisi jika menggunakan mode Static IP.',
            'static_gateway.ip' => 'Format Gateway tidak valid. Gunakan format IPv4 (contoh: 192.168.1.1).',
            'static_subnet.required_if' => 'Subnet Mask wajib diisi jika menggunakan mode Static IP.',
            'static_dns1.ip' => 'Format DNS 1 tidak valid. Gunakan format IPv4.',
            'static_dns2.ip' => 'Format DNS 2 tidak valid. Gunakan format IPv4.',
            'speed_profile_id.exists' => 'Speed Profile yang dipilih tidak ditemukan.',
        ];

        try {
            $data = $request->validate([
                'olt_id' => 'required|exists:olts,id',
                'serial_number' => 'required|string|max:255|unique:onus,serial_number',
                'mac_address' => 'nullable|string|max:255',
                'nama' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'ont_type' => 'nullable|string|max:255',
                'vendor' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'card' => 'nullable|integer|min:1',
                'port' => 'nullable|integer|min:1',
                'pelanggan_id' => 'nullable|exists:pelanggans,id',
                'odp_id' => 'nullable|exists:odps,id',
                'vlan_id' => 'nullable|integer|min:1|max:4096',
                'vlan_id_manual' => 'nullable|integer|min:1|max:4096',
                'wan_mode' => 'nullable|in:pppoe,dhcp,static,bridge',
                'pppoe_username' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
                'pppoe_password' => 'nullable|string|max:255|required_if:wan_mode,pppoe',
                'static_ip' => 'nullable|ip|required_if:wan_mode,static',
                'static_gateway' => 'nullable|ip|required_if:wan_mode,static',
                'static_subnet' => 'nullable|string|max:255|required_if:wan_mode,static',
                'static_dns1' => 'nullable|ip',
                'static_dns2' => 'nullable|ip',
                'speed_profile_id' => 'nullable|exists:speed_profiles,id',
            ], $messages);

            // Validate VLAN - must have either vlan_id or vlan_id_manual
            if (empty($data['vlan_id']) && empty($data['vlan_id_manual'])) {
                return back()->withInput()->withErrors([
                    'vlan_id' => 'VLAN ID wajib diisi. Pilih dari database atau masukkan manual.',
                ]);
            }

            $result = $this->onuProvisioning->registerOnu($data);

            if (!$result['success']) {
                $errorMessage = $result['message'] ?? 'Gagal registrasi ONU.';

                // Provide more helpful error messages
                if (strpos($errorMessage, 'SNMP') !== false || strpos($errorMessage, 'community') !== false) {
                    $errorMessage .= ' Pastikan write community string benar dan OLT dapat diakses.';
                } elseif (strpos($errorMessage, 'connection') !== false || strpos($errorMessage, 'koneksi') !== false) {
                    $errorMessage .= ' Pastikan OLT status Online dan dapat diakses dari server.';
                } elseif (strpos($errorMessage, 'tidak ditemukan') !== false) {
                    $errorMessage .= ' Pastikan ONU sudah terhubung secara fisik ke OLT di Card dan Port yang benar.';
                }

                return back()->withInput()->with('error', $errorMessage);
            }

            return redirect()->route('onus.show', $result['onu'])->with('success', $result['message'] ?? 'ONU berhasil diregistrasi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('ONU Register Error: ' . $e->getMessage(), [
                'request' => $request->except(['password', 'pppoe_password']),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi ONU: ' . $e->getMessage());
        }
    }
}

