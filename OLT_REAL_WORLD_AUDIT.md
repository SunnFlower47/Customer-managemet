# Audit Fitur OLT Monitoring - Real World Readiness

## 📊 Status Saat Ini

### ✅ **Yang Sudah Berfungsi dengan Baik**

1. **Database & Models**
   - ✅ Semua migration sudah lengkap
   - ✅ Models dengan relationships sudah benar
   - ✅ Fillable dan casts sudah sesuai

2. **Backend Architecture**
   - ✅ Driver system (Strategy Pattern) sudah solid
   - ✅ Service layer sudah terstruktur dengan baik
   - ✅ Controllers sudah lengkap (CRUD, actions)
   - ✅ Error handling sudah ada
   - ✅ Logging sudah diimplementasikan

3. **UI/UX**
   - ✅ Semua views sudah dibuat dan user-friendly
   - ✅ Form validation sudah lengkap
   - ✅ Real-time search untuk Pelanggan/ODP
   - ✅ Modals untuk create/edit
   - ✅ Dashboard dengan statistik
   - ✅ Filter dan search di semua halaman

4. **Permissions & Security**
   - ✅ Permissions sudah dibuat
   - ✅ Route protection sudah ada
   - ✅ Password encryption untuk OLT credentials

5. **Testing Support**
   - ✅ Mock driver untuk testing tanpa hardware
   - ✅ Test data seeder sudah ada

---

## ⚠️ **Yang Perlu Diimplementasikan untuk Real World**

### 1. **Driver Implementations (PENTING!)**

Driver-driver real (`ZteC300Driver`, `ZteC320Driver`, `GenericSnmpDriver`) masih memiliki banyak method yang **belum diimplementasikan**:

#### **Method yang Masih Stub/Empty:**

- ❌ `provisionOnu()` - Registrasi ONU ke OLT
- ❌ `discoverUnconfiguredOnus()` - Discovery ONU baru
- ❌ `configureService()` - Konfigurasi service (VLAN, WiFi, LAN)
- ❌ `rebootOnu()` - Reboot ONU remote
- ❌ `resetOnu()` - Reset ONU
- ❌ `disableOnu()` - Disable ONU
- ❌ `enableOnu()` - Enable ONU
- ❌ `getOnuDetails()` - Detail lengkap ONU
- ❌ `getBandwidthUsage()` - Monitoring bandwidth real-time
- ❌ `getOnuList()` - List ONU per port (masih kosong di ZteC300Driver)

#### **Yang Sudah Ada (Partial):**

- ✅ `testConnection()` - Test koneksi SNMP/Telnet
- ✅ `getSystemInfo()` - Info dasar OLT
- ✅ `getPonPorts()` - List PON ports (partial, perlu OID yang benar)
- ✅ `getStatistics()` - Statistik dasar

---

## 🔧 **Langkah-langkah untuk Menambahkan OLT Real**

### **Step 1: Siapkan Dokumentasi OLT**

Untuk setiap vendor/model OLT, Anda perlu:

1. **SNMP MIB Files**
   - Download MIB files dari vendor
   - Identifikasi OID untuk:
     - PON port status
     - ONU list per port
     - ONU details (signal, status, dll)
     - ONU provisioning commands
     - Service configuration

2. **CLI Commands (jika pakai Telnet/SSH)**
   - Command untuk register ONU
   - Command untuk reboot/reset/disable ONU
   - Command untuk configure service
   - Command untuk get ONU list

### **Step 2: Implementasi Driver Methods**

#### **Contoh untuk ZTE C300 (SNMP):**

```php
// backend/app/Drivers/ZteC300Driver.php

public function provisionOnu(array $data): array
{
    try {
        $this->connect();
        
        // SNMP SET untuk register ONU
        // OID dan value sesuai dokumentasi ZTE C300
        $oid = "1.3.6.1.4.1.3902.1015.1.1.1.1.2.{$data['card']}.{$data['port']}";
        $serialNumber = $data['serial_number'];
        
        // Set ONU registration
        $result = @snmpset(
            $this->olt->ip_address,
            $this->olt->snmp_community ?? 'private', // biasanya pakai private untuk write
            $oid,
            's', // string type
            $serialNumber,
            1000000,
            3
        );
        
        if ($result === false) {
            throw new Exception('Gagal register ONU via SNMP');
        }
        
        return [
            'success' => true,
            'message' => 'ONU berhasil diregistrasi',
            'onu_id' => $this->getOnuIdAfterRegistration($data),
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}

public function rebootOnu(string $serialNumber): array
{
    try {
        $this->connect();
        
        // SNMP SET untuk reboot ONU
        // OID reboot sesuai dokumentasi ZTE
        $onuData = $this->findOnuBySerial($serialNumber);
        if (!$onuData) {
            throw new Exception('ONU tidak ditemukan');
        }
        
        $oid = "1.3.6.1.4.1.3902.1015.1.1.1.1.3.{$onuData['card']}.{$onuData['port']}.{$onuData['onu_id']}";
        $result = @snmpset(
            $this->olt->ip_address,
            $this->olt->snmp_community ?? 'private',
            $oid,
            'i', // integer type
            1, // reboot command
            1000000,
            3
        );
        
        if ($result === false) {
            throw new Exception('Gagal reboot ONU');
        }
        
        return [
            'success' => true,
            'message' => 'Perintah reboot berhasil dikirim',
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}

public function discoverUnconfiguredOnus(): array
{
    try {
        $this->connect();
        
        $unconfigured = [];
        
        // Walk through ONU table untuk menemukan ONU yang belum terdaftar
        // OID discovery sesuai dokumentasi ZTE
        $onuTable = @snmprealwalk(
            $this->olt->ip_address,
            $this->olt->snmp_community ?? 'public',
            "1.3.6.1.4.1.3902.1015.1.1.1.1.4" // OID untuk unconfigured ONUs
        );
        
        foreach ($onuTable as $oid => $value) {
            // Parse OID untuk mendapatkan card, port, serial
            if (preg_match('/\.(\d+)\.(\d+)\.(\d+)$/', $oid, $matches)) {
                $card = (int) $matches[1];
                $port = (int) $matches[2];
                $onuIndex = (int) $matches[3];
                
                // Get serial number
                $serialOid = "1.3.6.1.4.1.3902.1015.1.1.1.1.5.{$card}.{$port}.{$onuIndex}";
                $serial = @snmpget(
                    $this->olt->ip_address,
                    $this->olt->snmp_community ?? 'public',
                    $serialOid
                );
                
                // Get signal
                $signalOid = "1.3.6.1.4.1.3902.1015.1.1.1.1.6.{$card}.{$port}.{$onuIndex}";
                $signal = @snmpget(
                    $this->olt->ip_address,
                    $this->olt->snmp_community ?? 'public',
                    $signalOid
                );
                
                if ($serial) {
                    $unconfigured[] = [
                        'serial_number' => trim($serial, '"'),
                        'card' => $card,
                        'port' => $port,
                        'signal' => $signal ? (float) $signal : null,
                        'vendor' => null,
                        'model' => null,
                    ];
                }
            }
        }
        
        return $unconfigured;
    } catch (Exception $e) {
        Log::error("Failed to discover unconfigured ONUs: " . $e->getMessage());
        return [];
    }
}
```

#### **Contoh untuk ZTE C320 (Telnet):**

```php
// backend/app/Drivers/ZteC320Driver.php

public function provisionOnu(array $data): array
{
    try {
        $this->connect();
        
        // Telnet command untuk register ONU
        $command = sprintf(
            "configure terminal\ninterface gpon-olt_%d/%d/%d\nonu %s type %s\n",
            $data['card'],
            $data['port'],
            $data['port'],
            $data['serial_number'],
            $data['ont_type'] ?? 'ZTE-F660'
        );
        
        $response = $this->executeCommand($command);
        
        // Check response untuk success
        if (strpos($response, 'success') !== false || strpos($response, 'OK') !== false) {
            return [
                'success' => true,
                'message' => 'ONU berhasil diregistrasi',
                'onu_id' => $this->extractOnuId($response),
            ];
        }
        
        throw new Exception('Registrasi gagal: ' . $response);
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
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
        
        $command = sprintf(
            "configure terminal\ninterface gpon-olt_%d/%d/%d\nonu %s reboot\n",
            $onuData['card'],
            $onuData['port'],
            $onuData['port'],
            $serialNumber
        );
        
        $response = $this->executeCommand($command);
        
        if (strpos($response, 'success') !== false || strpos($response, 'OK') !== false) {
            return [
                'success' => true,
                'message' => 'Perintah reboot berhasil dikirim',
            ];
        }
        
        throw new Exception('Reboot gagal: ' . $response);
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}
```

### **Step 3: Update OID Configuration**

Edit `backend/config/olt.php` dengan OID yang benar sesuai dokumentasi vendor:

```php
'zte' => [
    'c300' => [
        'driver' => \App\Drivers\ZteC300Driver::class,
        'connection_types' => ['snmp'],
        'default_port' => 161,
        'snmp_oids' => [
            // System
            'system_description' => '1.3.6.1.2.1.1.1.0',
            'system_uptime' => '1.3.6.1.2.1.1.3.0',
            
            // PON Ports
            'pon_port_table' => '1.3.6.1.4.1.3902.1015.1.1.1.1.1',
            'pon_port_status' => '1.3.6.1.4.1.3902.1015.1.1.1.1.2',
            
            // ONU Management
            'onu_table' => '1.3.6.1.4.1.3902.1015.1.1.1.1.3',
            'onu_serial' => '1.3.6.1.4.1.3902.1015.1.1.1.1.4',
            'onu_signal' => '1.3.6.1.4.1.3902.1015.1.1.1.1.5',
            'onu_status' => '1.3.6.1.4.1.3902.1015.1.1.1.1.6',
            
            // ONU Actions
            'onu_register' => '1.3.6.1.4.1.3902.1015.1.1.1.1.7',
            'onu_reboot' => '1.3.6.1.4.1.3902.1015.1.1.1.1.8',
            'onu_disable' => '1.3.6.1.4.1.3902.1015.1.1.1.1.9',
        ],
        'default_snmp_community' => 'public',
        'write_snmp_community' => 'private', // untuk write operations
    ],
],
```

### **Step 4: Testing dengan OLT Real**

1. **Setup OLT di Environment**
   ```bash
   # Pastikan PHP SNMP extension terinstall
   php -m | grep snmp
   
   # Jika belum, install:
   # Ubuntu/Debian: sudo apt-get install php-snmp
   # CentOS/RHEL: sudo yum install php-snmp
   ```

2. **Tambahkan OLT via Web UI**
   - Buka menu "OLT Monitoring → Daftar OLT"
   - Klik "Tambah OLT"
   - Isi:
     - Nama OLT
     - IP Address (IP real OLT)
     - Vendor: ZTE
     - Model: C300 atau C320
     - SNMP Community (biasanya "public" untuk read, "private" untuk write)
     - Port: 161 (default SNMP)
     - Username/Password (jika pakai Telnet/SSH)

3. **Test Connection**
   - Klik "Test Koneksi" di halaman detail OLT
   - Pastikan koneksi berhasil

4. **Sync Data**
   - Klik "Sinkron" untuk sync data dari OLT
   - Cek apakah PON ports dan ONUs terdeteksi

5. **Register ONU**
   - Buka "Register ONU"
   - Pilih OLT dan port
   - Masukkan serial number ONU
   - Submit dan cek apakah ONU terdaftar di OLT

---

## 📋 **Checklist Implementasi Real World**

### **Phase 1: Basic Connectivity** ✅
- [x] Database schema
- [x] Models & relationships
- [x] Basic driver structure
- [x] Test connection method
- [x] UI untuk add OLT

### **Phase 2: Driver Implementation** ⚠️
- [ ] Implement `provisionOnu()` untuk ZTE C300
- [ ] Implement `provisionOnu()` untuk ZTE C320
- [ ] Implement `discoverUnconfiguredOnus()` untuk semua driver
- [ ] Implement `rebootOnu()`, `resetOnu()`, `disableOnu()`, `enableOnu()`
- [ ] Implement `configureService()` untuk konfigurasi VLAN/WiFi/LAN
- [ ] Implement `getOnuDetails()` dengan data lengkap
- [ ] Implement `getBandwidthUsage()` untuk monitoring real-time
- [ ] Implement `getOnuList()` dengan parsing yang benar

### **Phase 3: Testing & Validation** ⚠️
- [ ] Test dengan OLT ZTE C300 real
- [ ] Test dengan OLT ZTE C320 real
- [ ] Test register ONU
- [ ] Test reboot/reset/disable ONU
- [ ] Test configure service
- [ ] Test sync data
- [ ] Test monitoring real-time

### **Phase 4: Additional Vendors** (Optional)
- [ ] Implement Huawei driver
- [ ] Implement Fiberhome driver
- [ ] Test dengan hardware masing-masing

---

## 🚨 **Catatan Penting**

1. **SNMP Community String**
   - `public` = read-only (untuk monitoring)
   - `private` = read-write (untuk configuration)
   - Pastikan OLT dikonfigurasi dengan community string yang benar

2. **Security**
   - Jangan hardcode credentials
   - Gunakan encryption untuk password (sudah ada)
   - Batasi akses SNMP hanya dari IP tertentu di OLT

3. **Error Handling**
   - Semua method driver harus return array dengan `success` dan `message`
   - Log semua error untuk debugging
   - Tampilkan error yang user-friendly di UI

4. **Performance**
   - SNMP operations bisa lambat untuk banyak ONU
   - Gunakan background jobs untuk sync besar
   - Cache data yang tidak sering berubah

5. **Dokumentasi Vendor**
   - Setiap vendor punya MIB dan command yang berbeda
   - Pastikan OID yang digunakan sesuai dengan firmware version OLT
   - Test di lab environment dulu sebelum production

---

## 📚 **Resources**

1. **ZTE Documentation**
   - ZTE C300 MIB files
   - ZTE C320 CLI commands
   - ZTE SNMP OID reference

2. **SNMP Tools untuk Testing**
   - `snmpwalk` - untuk explore OID
   - `snmpset` - untuk test write operations
   - `snmpget` - untuk test read operations

3. **Example Commands**
   ```bash
   # Test SNMP connection
   snmpget -v 2c -c public <OLT_IP> 1.3.6.1.2.1.1.1.0
   
   # Walk ONU table
   snmpwalk -v 2c -c public <OLT_IP> 1.3.6.1.4.1.3902.1015.1.1.1.1.3
   
   # Test write (register ONU)
   snmpset -v 2c -c private <OLT_IP> <OID> s "<serial_number>"
   ```

---

## ✅ **Kesimpulan**

**Status Saat Ini:**
- ✅ Backend architecture **SANGAT BAIK** dan siap untuk real world
- ✅ UI/UX **LENGKAP** dan user-friendly
- ⚠️ Driver implementations **MASIH PERLU** diimplementasikan sesuai dokumentasi vendor

**Untuk Production:**
1. Implement semua method driver sesuai dokumentasi vendor
2. Test dengan OLT real di lab environment
3. Validasi semua fitur (register, reboot, configure, dll)
4. Setup monitoring dan alerting
5. Dokumentasikan OID dan commands yang digunakan

**Estimasi Waktu:**
- Implementasi driver ZTE C300: **2-3 hari** (jika punya dokumentasi lengkap)
- Implementasi driver ZTE C320: **2-3 hari**
- Testing & validation: **1-2 hari**
- **Total: ~1 minggu** untuk 2 vendor

Sistem sudah **90% siap**, tinggal implementasi driver methods sesuai dokumentasi vendor OLT Anda.

