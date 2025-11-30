# Panduan Setup Auto Sync untuk OLT Monitoring

## Overview

Sistem OLT Monitoring memiliki **auto sync** yang berjalan otomatis untuk:
1. **Sync database OLT** - Mengambil data ONU dari OLT setiap 5 menit
2. **Monitor status OLT** - Mengecek status OLT setiap 30 detik
3. **Auto discover ONU** - Mencari ONU baru yang belum terdaftar setiap 10 menit

## Command yang Sudah Dikonfigurasi

Di file `routes/console.php`, sudah ada 3 scheduled commands:

```php
// 1. Monitor status OLT setiap 30 detik
Schedule::command('olts:monitor')
    ->everyThirtySeconds()
    ->description('Monitor all OLT devices status');

// 2. Sync database OLT setiap 5 menit
Schedule::command('olts:sync')
    ->everyFiveMinutes()
    ->description('Sync OLT database with devices');

// 3. Check unregistered ONU setiap 10 menit
Schedule::command('olts:check-unregistered')
    ->everyTenMinutes()
    ->description('Check and auto-discover unregistered ONUs');
```

## Cara Mengaktifkan Auto Sync

### Step 1: Setup Cron Job

Auto sync memerlukan **cron job** yang berjalan setiap menit untuk menjalankan Laravel scheduler.

#### Linux/Ubuntu/Debian

1. Buka crontab:
   ```bash
   crontab -e
   ```

2. Tambahkan baris berikut (sesuaikan path project):
   ```bash
   * * * * * cd /path-to-project/backend && php artisan schedule:run >> /dev/null 2>&1
   ```
    
   **Contoh path**:
   ```bash
   * * * * * cd /var/www/customer-wifi-management/backend && php artisan schedule:run >> /dev/null 2>&1
   ```

3. Simpan dan keluar (tekan `Ctrl+X`, lalu `Y`, lalu `Enter`)

4. Verifikasi cron job aktif:
   ```bash
   crontab -l
   ```

#### Windows (Menggunakan Task Scheduler)

1. Buka **Task Scheduler** (Windows)

2. Create Basic Task:
   - Name: `Laravel Scheduler`
   - Trigger: **Daily** (atau sesuai kebutuhan)
   - Action: **Start a program**
   - Program: `php.exe`
   - Arguments: `artisan schedule:run`
   - Start in: `C:\path-to-project\backend`

3. Atau gunakan **Windows Task Scheduler** dengan script batch:
   ```batch
   @echo off
   cd C:\path-to-project\backend
   php artisan schedule:run
   ```

#### Windows (Menggunakan Herd - Development)

Jika menggunakan **Laravel Herd** untuk development, scheduler sudah otomatis aktif. Tidak perlu setup cron job.

### Step 2: Verifikasi Scheduler Berjalan

Test manual apakah scheduler berjalan:

```bash
# Test run scheduler
php artisan schedule:run

# Lihat list scheduled commands
php artisan schedule:list
```

Output `schedule:list` akan menampilkan:
```
+----------------+------------------+----------+
| Command        | Interval         | Next Run |
+----------------+------------------+----------+
| olts:monitor   | Every 30 seconds | ...      |
| olts:sync      | Every 5 minutes  | ...      |
| olts:check-... | Every 10 minutes | ...      |
+----------------+------------------+----------+
```

### Step 3: Test Manual Command

Sebelum mengaktifkan auto sync, test manual dulu:

```bash
# Test sync semua OLT
php artisan olts:sync

# Test sync OLT tertentu
php artisan olts:sync --olt=1

# Test monitor OLT
php artisan olts:monitor

# Test check unregistered ONU
php artisan olts:check-unregistered
```

## Detail Auto Sync Commands

### 1. `olts:monitor` (Setiap 30 Detik)

**Fungsi**: Monitor status semua OLT (online/offline/error)

**Apa yang dilakukan**:
- Test koneksi ke semua OLT aktif
- Update status OLT di database
- Update `last_checked_at` timestamp

**Impact**: Ringan, hanya test koneksi

### 2. `olts:sync` (Setiap 5 Menit)

**Fungsi**: Sync database dengan OLT devices

**Apa yang dilakukan**:
- Ambil data PON ports dari OLT
- Ambil daftar ONU dari setiap port
- Update ONU yang sudah ada
- Tambahkan ONU baru ke database

**Impact**: Sedang-berat, tergantung jumlah port dan ONU

**Catatan**: 
- Proses bisa memakan waktu beberapa menit untuk OLT besar
- Disarankan untuk OLT dengan banyak ONU, interval bisa diperpanjang

### 3. `olts:check-unregistered` (Setiap 10 Menit)

**Fungsi**: Auto-discover ONU baru yang belum terdaftar

**Apa yang dilakukan**:
- Scan semua OLT aktif
- Cari ONU yang belum terdaftar di database
- Tambahkan ONU baru ke database

**Impact**: Sedang, scan semua OLT

## Konfigurasi Interval

Jika ingin mengubah interval, edit file `routes/console.php`:

```php
// Contoh: Ubah sync menjadi setiap 10 menit (lebih ringan)
Schedule::command('olts:sync')
    ->everyTenMinutes()  // Ubah dari everyFiveMinutes()
    ->description('Sync OLT database with devices');

// Contoh: Ubah monitor menjadi setiap 1 menit
Schedule::command('olts:monitor')
    ->everyMinute()  // Ubah dari everyThirtySeconds()
    ->description('Monitor all OLT devices status');
```

**Opsi interval yang tersedia**:
- `->everyMinute()` - Setiap 1 menit
- `->everyFiveMinutes()` - Setiap 5 menit
- `->everyTenMinutes()` - Setiap 10 menit
- `->everyThirtyMinutes()` - Setiap 30 menit
- `->hourly()` - Setiap 1 jam
- `->daily()` - Setiap hari
- `->dailyAt('14:00')` - Setiap hari jam 14:00
- `->cron('*/5 * * * *')` - Custom cron expression (setiap 5 menit)

## Monitoring Auto Sync

### Cek Log Scheduler

Laravel scheduler akan log ke `storage/logs/laravel.log`:

```bash
# Lihat log terbaru
tail -f storage/logs/laravel.log

# Cari log scheduler
grep "schedule" storage/logs/laravel.log
```

### Cek Sync Log di Database

Sync log tersimpan di tabel `olt_sync_logs`:

```sql
-- Lihat sync log terbaru
SELECT * FROM olt_sync_logs 
ORDER BY created_at DESC 
LIMIT 10;

-- Lihat sync yang gagal
SELECT * FROM olt_sync_logs 
WHERE status = 'failed' 
ORDER BY created_at DESC;
```

Atau via tinker:

```bash
php artisan tinker
>>> \App\Models\OltSyncLog::latest()->take(5)->get();
```

## Troubleshooting

### Auto Sync Tidak Berjalan

**Kemungkinan penyebab**:
1. Cron job tidak aktif
2. Path project salah di crontab
3. PHP path salah
4. Permission issue

**Solusi**:
1. Cek cron job aktif:
   ```bash
   crontab -l
   ```

2. Test manual scheduler:
   ```bash
   php artisan schedule:run
   ```

3. Cek log untuk error:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. Pastikan path benar di crontab (gunakan absolute path)

### Sync Terlalu Sering / Overload OLT

**Solusi**: Perpanjang interval di `routes/console.php`:

```php
// Ubah dari 5 menit menjadi 15 menit
Schedule::command('olts:sync')
    ->everyFifteenMinutes()
    ->description('Sync OLT database with devices');
```

### Sync Gagal Terus

**Kemungkinan penyebab**:
1. OLT tidak dapat diakses
2. Community string salah
3. SNMP service tidak aktif

**Solusi**:
1. Cek sync log untuk detail error
2. Test koneksi manual: `snmpwalk -v2c -c public <IP_OLT>`
3. Verifikasi community string di OLT

## Disable Auto Sync

Jika ingin menonaktifkan auto sync sementara:

### Metode 1: Comment di routes/console.php

```php
// Comment scheduled commands
// Schedule::command('olts:sync')
//     ->everyFiveMinutes()
//     ->description('Sync OLT database with devices');
```

### Metode 2: Hapus Cron Job

```bash
# Edit crontab
crontab -e

# Hapus atau comment baris scheduler
# * * * * * cd /path-to-project/backend && php artisan schedule:run >> /dev/null 2>&1
```

### Metode 3: Tambahkan Condition

Tambahkan kondisi di `routes/console.php`:

```php
// Hanya aktifkan jika env variable true
if (env('OLT_AUTO_SYNC_ENABLED', false)) {
    Schedule::command('olts:sync')
        ->everyFiveMinutes()
        ->description('Sync OLT database with devices');
}
```

Lalu di `.env`:
```env
OLT_AUTO_SYNC_ENABLED=true  # atau false untuk disable
```

## Best Practices

### 1. Interval yang Disarankan

- **Monitor status**: Setiap 30 detik - 1 menit (ringan)
- **Sync database**: Setiap 5-15 menit (sedang-berat)
- **Auto discover**: Setiap 10-30 menit (sedang)

### 2. Production vs Development

**Development**:
- Bisa lebih sering untuk testing
- Monitor setiap 30 detik
- Sync setiap 5 menit

**Production**:
- Sesuaikan dengan beban OLT
- Monitor setiap 1-2 menit
- Sync setiap 10-15 menit (untuk OLT besar)

### 3. Monitoring

- Setup alert jika sync gagal terus
- Monitor log untuk error
- Cek sync log secara berkala

### 4. Performance

- Gunakan queue untuk sync yang berat
- Pastikan queue worker berjalan
- Monitor resource usage (CPU, memory)

## Command Reference

```bash
# Manual run scheduler (test)
php artisan schedule:run

# List scheduled commands
php artisan schedule:list

# Test sync manual
php artisan olts:sync
php artisan olts:sync --olt=1

# Test monitor manual
php artisan olts:monitor

# Test auto discover manual
php artisan olts:check-unregistered
```

## Kesimpulan

Auto sync sudah dikonfigurasi dan siap digunakan. Yang perlu dilakukan:

1. ✅ **Setup cron job** (jika belum ada)
2. ✅ **Test manual** command dulu
3. ✅ **Monitor log** untuk memastikan berjalan
4. ✅ **Sesuaikan interval** sesuai kebutuhan

Setelah setup, sistem akan otomatis:
- Monitor status OLT setiap 30 detik
- Sync database setiap 5 menit
- Auto-discover ONU baru setiap 10 menit

---

**💡 Tips**: Untuk pertama kali, lakukan manual sync dulu untuk memastikan semua OLT bisa diakses dan sync berjalan dengan baik.

