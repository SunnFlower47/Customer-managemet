# Panduan Testing OLT Monitoring Tanpa Perangkat Fisik

## Cara Menggunakan Mock Driver

### 1. Jalankan Seeder untuk Data Dummy

```bash
php artisan db:seed --class=OltTestDataSeeder
```

Seeder ini akan membuat:
- 1 OLT dengan IP `127.0.0.1` (otomatis menggunakan MockOltDriver)
- 4 VLAN (2058, 100, 200)
- 4 Speed Profiles (10, 20, 50, 100 Mbps)
- 4 ONU dengan berbagai status (online/offline)
- PON Ports dan Services

### 2. Cara Kerja Mock Mode

Mock Driver akan aktif otomatis jika:
- OLT memiliki IP address `127.0.0.1` atau `localhost`
- Atau set environment variable `OLT_MOCK_MODE=true` di `.env`

### 3. Fitur yang Bisa Di-test

#### ✅ OLT Management
- [x] Tambah OLT baru (gunakan IP 127.0.0.1 untuk mock)
- [x] Test Koneksi (akan return success dengan delay simulasi)
- [x] Lihat Detail OLT (system info, PON ports, statistics)
- [x] Sinkronisasi OLT (akan return data mock)

#### ✅ ONU Management
- [x] Register ONU baru (provisioning akan sukses)
- [x] Lihat Daftar ONU (dengan filter)
- [x] Lihat Detail ONU (signal, status, config)
- [x] Reboot ONU (simulasi sukses)
- [x] Reset ONU (simulasi sukses)
- [x] Disable/Enable ONU (simulasi sukses)

#### ✅ Monitoring
- [x] Dashboard OLT (statistics, fan monitoring)
- [x] Real-time monitoring (data mock)
- [x] Bandwidth usage (data random)
- [x] Alarms (data mock)

#### ✅ Service Configuration
- [x] Register ONU dengan berbagai mode WAN:
  - PPPoE (dengan username/password)
  - DHCP (otomatis)
  - Static IP (dengan IP, gateway, subnet, DNS)
  - Bridge (hanya VLAN)

#### ✅ VLAN & Speed Profile
- [x] CRUD VLAN Database
- [x] CRUD Speed Profiles

### 4. Testing Workflow

#### Step 1: Setup Data Dummy
```bash
php artisan db:seed --class=OltTestDataSeeder
```

#### Step 2: Akses Dashboard
- Buka: `/olts` atau `/dashboard/olt`
- Lihat OLT "OLT Testing (Mock Mode)" sudah muncul

#### Step 3: Test Fitur OLT
1. Klik OLT → Test Koneksi (akan sukses dengan delay 0.5s)
2. Klik Sinkron → Akan sync data mock
3. Lihat PON Ports → Akan muncul 8 port (6 up, 2 down)

#### Step 4: Test Register ONU
1. Buka: `/onus/register`
2. Pilih OLT "OLT Testing (Mock Mode)"
3. Isi form dengan mode WAN yang berbeda:
   - **PPPoE**: Isi username & password
   - **DHCP**: Hanya pilih VLAN & Speed Profile
   - **Static IP**: Isi IP, Gateway, Subnet, DNS
   - **Bridge**: Hanya pilih VLAN
4. Submit → Akan sukses (simulasi)

#### Step 5: Test ONU Management
1. Buka: `/onus`
2. Filter berdasarkan OLT, status, signal
3. Klik ONU → Lihat detail
4. Test action: Reboot, Reset, Disable, Enable

#### Step 6: Test Monitoring
1. Buka: `/dashboard/olt`
2. Lihat statistics (data mock)
3. Lihat timeline kesehatan OLT
4. Lihat aktivitas ONU

### 5. Data Mock yang Tersedia

#### Unconfigured ONUs (untuk Register)
Mock driver akan return 2 ONU unconfigured setiap kali:
- `ZTEGC[random]` - ZTE F601
- `YYKFC[random]` - Huawei HG8245H

#### ONU List (untuk Sync)
Mock driver akan return 4 ONU:
- 2 ONU online di port 1/1/1
- 1 ONU online di port 1/1/2
- 1 ONU offline di port 1/1/2

#### Statistics
- CPU Usage: 25.5%
- Memory Usage: 45.2%
- Temperature: 42°C
- Fan Speed: ~2690 RPM
- Uptime: 45 days

### 6. Tips Testing

1. **Gunakan IP 127.0.0.1** untuk semua OLT test agar otomatis mock
2. **Data akan berbeda setiap refresh** untuk beberapa fitur (bandwidth, unconfigured ONUs)
3. **Delay simulasi** ada di beberapa operasi (0.3-0.5 detik) untuk realisme
4. **Semua operasi akan sukses** kecuali jika ONU tidak ditemukan

### 7. Troubleshooting

#### Mock Driver tidak aktif?
- Pastikan IP OLT adalah `127.0.0.1` atau `localhost`
- Atau set `OLT_MOCK_MODE=true` di `.env`

#### Data tidak muncul?
- Jalankan seeder: `php artisan db:seed --class=OltTestDataSeeder`
- Pastikan migration sudah dijalankan: `php artisan migrate`

#### Error saat test connection?
- Mock driver selalu return success, jika error berarti ada masalah di kode lain

### 8. File yang Terlibat

- `app/Drivers/MockOltDriver.php` - Driver mock untuk testing
- `app/Services/OltDriverFactory.php` - Factory yang memilih driver
- `database/seeders/OltTestDataSeeder.php` - Seeder data dummy

### 9. Next Steps

Setelah testing dengan mock driver selesai, untuk production:
1. Ganti IP OLT dengan IP real
2. Update vendor/model sesuai perangkat
3. Mock driver akan otomatis tidak digunakan
4. Driver real (ZteC300Driver, ZteC320Driver, dll) akan digunakan

---

**Happy Testing! 🚀**

