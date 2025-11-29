# Panduan SNMP untuk OLT Monitoring

## 1. Pengenalan SNMP

SNMP (Simple Network Management Protocol) adalah protokol standar untuk monitoring dan manajemen perangkat jaringan, termasuk OLT (Optical Line Terminal).

### Versi SNMP

- **SNMPv1**: Versi awal, sederhana, menggunakan community string (mirip password)
- **SNMPv2c**: Versi yang paling umum digunakan, lebih baik dari v1, masih pakai community string
- **SNMPv3**: Versi paling aman, menggunakan username, password, dan enkripsi

### Community String

Community string berfungsi seperti password untuk akses SNMP:

- **public**: Default untuk read-only (hanya membaca data)
- **private**: Default untuk read-write (bisa membaca dan menulis/konfigurasi)

**⚠️ PENTING**: Jangan gunakan default `public`/`private` di production! Gunakan community string yang unik dan aman.

## 2. Persiapan OLT untuk SNMP

### Pastikan OLT Mendukung SNMP

Hampir semua OLT modern (ZTE, Huawei, Fiberhome, dll) memiliki SNMP agent built-in. Pastikan:

1. SNMP service aktif di OLT
2. Community string sudah dikonfigurasi
3. IP management interface dapat diakses dari server

### Konfigurasi SNMP di OLT

Biasanya dilakukan via:
- Web interface OLT (jika tersedia)
- CLI/Telnet/SSH ke OLT
- Vendor-specific management tool

**Contoh konfigurasi SNMP di ZTE C300 (via CLI)**:
```
configure terminal
snmp-server community public ro
snmp-server community private rw
snmp-server enable
exit
write
```

## 3. Testing Koneksi SNMP

### Sebelum Coding, Test Dulu!

Sebelum mengintegrasikan ke website, pastikan koneksi SNMP berfungsi dari command line:

#### Windows (PowerShell/CMD)

```powershell
# Install SNMP tools (jika belum ada)
# Download dari: https://www.net-snmp.org/download.html

# Test dengan snmpwalk
snmpwalk -v2c -c public 192.168.1.1

# Test dengan snmpget (ambil satu OID)
snmpget -v2c -c public 192.168.1.1 1.3.6.1.2.1.1.1.0
```

#### Linux/Mac

```bash
# Install net-snmp (jika belum ada)
sudo apt-get install snmp snmp-mibs-downloader  # Ubuntu/Debian
sudo yum install net-snmp net-snmp-utils        # CentOS/RHEL

# Test dengan snmpwalk
snmpwalk -v2c -c public 192.168.1.1

# Test dengan snmpget
snmpget -v2c -c public 192.168.1.1 1.3.6.1.2.1.1.1.0
```

### Parameter SNMP Command

- `-v2c`: Versi SNMP (bisa `-v1` atau `-v2c` atau `-v3`)
- `-c public`: Community string
- `192.168.1.1`: IP address OLT
- `1.3.6.1.2.1.1.1.0`: OID (Object Identifier) yang ingin dibaca

### Jika Berhasil

Akan muncul output seperti:
```
SNMPv2-MIB::sysDescr.0 = STRING: ZTE C300 OLT System
SNMPv2-MIB::sysUpTime.0 = Timeticks: (123456) 0:20:34.56
...
```

### Jika Gagal

Kemungkinan masalah:
- OLT tidak dapat diakses (cek IP, firewall, network)
- Community string salah
- SNMP service tidak aktif di OLT
- Port 161 UDP terblokir firewall

## 4. OID (Object Identifier)

OID adalah alamat unik untuk setiap data di perangkat SNMP. Formatnya berupa angka yang dipisahkan titik, contoh: `1.3.6.1.2.1.1.1.0`

### OID Standar (MIB-II)

OID standar yang tersedia di hampir semua perangkat:

| OID | Deskripsi |
|-----|-----------|
| `1.3.6.1.2.1.1.1.0` | System Description |
| `1.3.6.1.2.1.1.3.0` | System Uptime |
| `1.3.6.1.2.1.1.5.0` | System Name |
| `1.3.6.1.2.1.2.2.1.10` | Interface Inbound Traffic |
| `1.3.6.1.2.1.2.2.1.16` | Interface Outbound Traffic |

### OID Vendor-Specific

Setiap vendor memiliki OID khusus untuk fitur spesifik mereka:

**ZTE C300** (Enterprise OID: `1.3.6.1.4.1.3902`):
- `1.3.6.1.4.1.3902.1015.1.1.1.1.1.0` - PON Port Count
- `1.3.6.1.4.1.3902.1015.1.1.1.1.2` - ONU Table
- `1.3.6.1.4.1.3902.1015.1.1.1.1.3` - ONU Serial Number
- `1.3.6.1.4.1.3902.1015.1.1.1.1.4` - ONU Signal (RX Power)

**Huawei** (Enterprise OID: `1.3.6.1.4.1.2011`):
- OID berbeda, perlu download MIB file dari Huawei

### MIB Files

MIB (Management Information Base) adalah file yang menjelaskan OID untuk vendor tertentu. Download dari:
- Website vendor (ZTE, Huawei, Fiberhome, dll)
- Load ke SNMP tool untuk memudahkan membaca OID

## 5. Integrasi ke Website (PHP)

### PHP SNMP Extension

Pastikan PHP SNMP extension terinstall:

```bash
# Ubuntu/Debian
sudo apt-get install php-snmp

# CentOS/RHEL
sudo yum install php-snmp

# Restart web server
sudo systemctl restart apache2  # atau nginx + php-fpm
```

### Cek SNMP Extension

```php
<?php
if (function_exists('snmpget')) {
    echo "SNMP extension tersedia";
} else {
    echo "SNMP extension TIDAK tersedia";
}
?>
```

### Contoh Penggunaan di PHP

```php
<?php
// SNMP GET (ambil satu nilai)
$result = snmpget(
    '192.168.1.1',        // IP OLT
    'public',             // Community string
    '1.3.6.1.2.1.1.1.0', // OID
    1000000,              // Timeout (microseconds)
    3                     // Retries
);

echo $result; // Output: ZTE C300 OLT System

// SNMP WALK (ambil semua nilai dalam tree)
$walk = snmprealwalk(
    '192.168.1.1',
    'public',
    '1.3.6.1.2.1.1',  // Base OID
    1000000,
    3
);

print_r($walk);

// SNMP SET (untuk konfigurasi - perlu write community)
$result = snmpset(
    '192.168.1.1',
    'private',              // Write community
    '1.3.6.1.4.1.3902.1015.1.1.1.1.6.1.1.1', // OID
    's',                    // Type: s=string, i=integer
    'ZTEG12345678',         // Value
    1000000,
    3
);
?>
```

### SNMP Versi 2c

```php
<?php
// Gunakan snmp2_get untuk SNMPv2c
if (function_exists('snmp2_get')) {
    $result = snmp2_get(
        '192.168.1.1',
        'public',
        '1.3.6.1.2.1.1.1.0',
        1000000,
        3
    );
} else {
    // Fallback ke v1
    $result = snmpget(
        '192.168.1.1',
        'public',
        '1.3.6.1.2.1.1.1.0',
        1000000,
        3
    );
}
?>
```

## 6. Implementasi di Sistem OLT Monitoring

### Driver System

Sistem ini menggunakan **Strategy Pattern** dengan driver per vendor:

- `BaseOltDriver`: Base class dengan helper method SNMP
- `ZteC300Driver`: Driver khusus untuk ZTE C300
- `ZteC320Driver`: Driver khusus untuk ZTE C320
- `GenericSnmpDriver`: Driver generic untuk vendor lain

### Helper Methods

Semua driver menggunakan helper method dari `BaseOltDriver`:

```php
// SNMP GET dengan auto-version detection
$result = $this->snmpGet('1.3.6.1.2.1.1.1.0');

// SNMP SET dengan auto-version detection
$success = $this->snmpSet('1.3.6.1.4.1.3902.1015.1.1.1.1.6.1.1.1', 's', 'ZTEG12345678');

// SNMP WALK dengan auto-version detection
$walk = $this->snmpWalk('1.3.6.1.4.1.3902.1015.1.1.1.1.2');
```

### Konfigurasi SNMP Version

Di form OLT, pilih SNMP version:
- **SNMPv1**: Untuk OLT lama yang hanya support v1
- **SNMPv2c**: **Recommended** untuk OLT modern (default)
- **SNMPv3**: Untuk keamanan tinggi (masih dalam pengembangan)

## 7. Best Practices & Security

### ⚠️ Security Checklist

1. **Jangan gunakan default community string** (`public`/`private`) di production
2. **Gunakan community string yang kuat** (minimal 8 karakter, kombinasi huruf/angka)
3. **Batasi akses SNMP** hanya dari IP server monitoring
4. **Gunakan SNMPv3** jika memungkinkan (lebih aman)
5. **Enkripsi password** di database (sudah diimplementasikan)

### Firewall Configuration

Pastikan port **161 UDP** terbuka dari server ke OLT:

```bash
# Contoh iptables (Linux)
sudo iptables -A INPUT -p udp --dport 161 -s 192.168.1.100 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 161 -j DROP
```

### Rate Limiting

Jangan poll terlalu cepat! Bisa membuat OLT overload:

- **Monitoring status**: Setiap 30-60 detik
- **Traffic monitoring**: Setiap 10-30 detik
- **Detail ONU**: On-demand (saat user request)

Sistem ini menggunakan:
- **Caching**: Cache hasil SNMP selama 30 detik
- **Background jobs**: Heavy operations di background
- **Queue system**: Untuk operasi yang tidak urgent

## 8. Troubleshooting

### Error: "SNMP extension tidak tersedia"

**Solusi**:
```bash
# Install PHP SNMP extension
sudo apt-get install php-snmp  # Ubuntu/Debian
sudo yum install php-snmp       # CentOS/RHEL

# Restart web server
sudo systemctl restart apache2
# atau
sudo systemctl restart php-fpm
```

### Error: "Timeout" atau "No response"

**Kemungkinan penyebab**:
1. OLT tidak dapat diakses (cek IP, network, firewall)
2. Port 161 UDP terblokir
3. Community string salah
4. SNMP service tidak aktif di OLT

**Solusi**:
1. Test dari command line dulu: `snmpwalk -v2c -c public <IP_OLT>`
2. Cek firewall: `telnet <IP_OLT> 161`
3. Verifikasi community string di OLT
4. Pastikan SNMP service aktif di OLT

### Error: "Authentication failed"

**Kemungkinan penyebab**:
- Community string salah
- Menggunakan read community untuk operasi write

**Solusi**:
- Pastikan community string benar
- Untuk operasi write (register ONU, reboot, dll), gunakan write community (biasanya `private`)

### Error: "No such instance" atau "OID tidak ditemukan"

**Kemungkinan penyebab**:
- OID tidak valid untuk OLT tersebut
- Firmware OLT berbeda (OID berubah)
- Vendor/model tidak sesuai

**Solusi**:
- Download MIB file dari vendor
- Verifikasi OID dengan `snmpwalk`
- Update driver dengan OID yang benar

## 9. Testing Tanpa OLT Fisik

Untuk testing tanpa OLT fisik, sistem ini menyediakan:

1. **Mock Driver**: Gunakan IP `127.0.0.1` atau `localhost`
2. **Test Data Seeder**: `php artisan db:seed --class=OltTestDataSeeder`

Lihat `OLT_TESTING_GUIDE.md` untuk detail lengkap.

## 10. Referensi

- **SNMP RFC**: https://tools.ietf.org/html/rfc1157
- **Net-SNMP**: https://www.net-snmp.org/
- **PHP SNMP**: https://www.php.net/manual/en/book.snmp.php
- **ZTE MIB**: Download dari website ZTE
- **Huawei MIB**: Download dari website Huawei

## 11. Contoh OID untuk Monitoring OLT

### System Information
```
1.3.6.1.2.1.1.1.0  → System Description
1.3.6.1.2.1.1.3.0  → System Uptime
1.3.6.1.2.1.1.5.0  → System Name
```

### Interface Statistics
```
1.3.6.1.2.1.2.2.1.10  → Interface Inbound Octets
1.3.6.1.2.1.2.2.1.16  → Interface Outbound Octets
1.3.6.1.2.1.2.2.1.8   → Interface Admin Status
1.3.6.1.2.1.2.2.1.7   → Interface Oper Status
```

### ZTE C300 Specific
```
1.3.6.1.4.1.3902.1015.1.1.1.1.1.0  → PON Port Count
1.3.6.1.4.1.3902.1015.1.1.1.1.2    → ONU Table
1.3.6.1.4.1.3902.1015.1.1.1.1.3    → ONU Serial Number
1.3.6.1.4.1.3902.1015.1.1.1.1.4    → ONU Signal (RX Power)
1.3.6.1.4.1.3902.1015.1.1.1.1.5    → ONU Status
```

---

**Catatan**: OID di atas adalah contoh. Pastikan OID sesuai dengan dokumentasi vendor dan firmware OLT Anda.

