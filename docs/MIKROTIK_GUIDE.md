# 🖥️ MikroTik Integration Guide

## 📋 Overview

Sistem ini terintegrasi dengan RouterOS API untuk memantau dan mengelola router MikroTik secara langsung dari web interface. Fitur ini memungkinkan admin untuk:

- ✅ Mengelola konfigurasi router MikroTik
- ✅ Mencari dan memantau PPPoE users
- ✅ Melihat status router dan resource usage
- ✅ Auto-check status PPPoE untuk setiap pelanggan
- ✅ Dashboard monitoring untuk semua router

## 🚀 Quick Start

### 1. Persiapan Router MikroTik

#### Aktifkan API Service
```bash
# Login ke RouterOS via Winbox atau Terminal
/ip service
set api disabled=no port=8728
```

#### Buat User API (Opsional)
```bash
# Buat user khusus untuk API (lebih aman)
/user add name=api-user password=your-secure-password group=full

# Atau gunakan user admin yang sudah ada
```

#### Verifikasi API Aktif
```bash
# Cek status API service
/ip service print where name=api
```

### 2. Menambahkan Router ke Sistem

1. **Login ke Admin Panel**
2. **Klik menu "MikroTik"** di sidebar
3. **Klik "Tambah MikroTik"**
4. **Isi form dengan informasi router:**
   - **Nama Router**: Nama untuk identifikasi (contoh: "Router Utama")
   - **IP Address**: IP router yang dapat diakses dari server
   - **Port**: Port API (default: 8728)
   - **RouterOS Version**: Versi RouterOS yang digunakan
   - **Username**: Username untuk API access
   - **Password**: Password untuk API access
   - **Lokasi**: Lokasi fisik router (opsional)
5. **Klik "Simpan & Test Koneksi"**
6. **Sistem akan otomatis test koneksi** dan menampilkan hasil

### 3. Menggunakan Fitur MikroTik

#### Dashboard Router
- **Lihat status koneksi** (Online/Offline/Error)
- **Monitor resource usage** (CPU, Memory, Uptime)
- **Lihat jumlah PPPoE aktif**
- **Test koneksi** manual

#### Search PPPoE
1. **Buka dashboard router** (`/mikrotiks/{id}`)
2. **Masukkan username PPPoE** di form search
3. **Klik "Cari"**
4. **Lihat hasil** - jika ditemukan, akan menampilkan detail lengkap

#### Auto-Check di Pelanggan
- **Otomatis** saat membuka halaman detail pelanggan
- **Menampilkan badge** "Ada di MikroTik" atau "Tidak ada di MikroTik"
- **Menampilkan info router** (nama router, IP, profile, status)
- **Update status** di database

## 📊 Fitur Lengkap

### Router Management

#### List Router
- **Tampilan**: Card layout untuk mobile, table untuk desktop
- **Filter**: By status (Online/Offline/Error)
- **Search**: By nama, IP, atau lokasi
- **Status Badge**: Visual indicator untuk status koneksi

#### Tambah Router
- **Form lengkap** dengan validasi
- **Panduan accordion** untuk setup RouterOS
- **Auto test koneksi** setelah simpan
- **Error handling** jika koneksi gagal

#### Edit Router
- **Update informasi** router
- **Change password** (opsional)
- **Toggle aktif/nonaktif**
- **Auto test** jika IP/port/username berubah

#### Dashboard Router
- **Status Overview**: Online/Offline/Error
- **PPPoE Active Count**: Jumlah PPPoE yang sedang aktif
- **Resource Usage**: CPU, Memory, Uptime (jika tersedia)
- **PPPoE Search**: Form untuk mencari username
- **Test Connection**: Tombol untuk test koneksi manual

### PPPoE Integration

#### Auto-Check System
- **Trigger**: Saat membuka halaman detail pelanggan
- **Process**: 
  1. Ambil PPPoE username dari pelanggan
  2. Cek di semua router aktif
  3. Update status di database
  4. Tampilkan badge di halaman
- **Non-Breaking**: Jika router offline atau error, sistem tetap berjalan normal

#### Status Display
- **"Ada di MikroTik"** (Hijau):
  - Menampilkan nama router
  - IP address yang didapat
  - Profile yang digunakan
  - Status di router (Active/Disabled)
- **"Tidak ada di MikroTik"** (Merah):
  - Menampilkan pesan bahwa PPPoE tidak ditemukan
  - Last checked timestamp

### Security Features

#### Password Encryption
- **Password di-encrypt** menggunakan Laravel encryption
- **Tidak disimpan** dalam plain text
- **Decrypt otomatis** saat digunakan untuk koneksi

#### Permission-Based Access
- **view-mikrotik**: Untuk melihat list dan detail router
- **manage-mikrotik**: Untuk CRUD router
- **Default**: Hanya admin yang memiliki permission ini

#### Error Handling
- **Graceful degradation**: Jika router offline, sistem tetap berjalan
- **Error logging**: Semua error dicatat di log
- **User-friendly messages**: Pesan error yang jelas

## 🔧 Troubleshooting

### Koneksi Gagal

#### Problem: "Koneksi gagal: Connection refused"
**Solutions**:
1. Pastikan API service aktif di RouterOS
2. Cek firewall tidak memblokir port 8728
3. Pastikan IP address benar dan dapat diakses dari server
4. Test dengan ping dari server ke router IP

#### Problem: "Koneksi gagal: Login failed"
**Solutions**:
1. Pastikan username dan password benar
2. Pastikan user memiliki permission API (group=full)
3. Cek apakah user tidak di-disable
4. Coba login manual via Winbox dengan credential yang sama

#### Problem: "Koneksi gagal: Timeout"
**Solutions**:
1. Cek koneksi jaringan antara server dan router
2. Pastikan router tidak overload
3. Cek apakah ada firewall atau NAT yang menghalangi
4. Coba increase timeout di code (jika diperlukan)

### PPPoE Tidak Ditemukan

#### Problem: PPPoE ada di router tapi tidak terdeteksi
**Solutions**:
1. Pastikan username PPPoE di database sama persis dengan di router
2. Cek case sensitivity (huruf besar/kecil)
3. Pastikan router yang benar dipilih
4. Coba search manual di dashboard router

#### Problem: Status tidak update otomatis
**Solutions**:
1. Refresh halaman detail pelanggan
2. Cek apakah router aktif dan online
3. Cek log untuk error messages
4. Coba test connection manual di dashboard router

## 📝 API Commands Reference

### RouterOS API Commands yang Digunakan

#### Get System Identity
```bash
/system/identity/print
```

#### Get Resource Usage
```bash
/system/resource/print
```

#### Search PPPoE User
```bash
/ppp/secret/print ?name=username
```

#### Get Active PPPoE Users
```bash
/ppp/active/print
```

## 🎯 Best Practices

### Security
1. **Gunakan user khusus** untuk API, bukan admin
2. **Gunakan password kuat** untuk API user
3. **Limit IP access** jika memungkinkan (di RouterOS)
4. **Monitor connection logs** secara berkala

### Performance
1. **Jangan terlalu sering** auto-check (sudah di-handle dengan caching)
2. **Gunakan connection pooling** jika banyak router
3. **Monitor resource usage** router secara berkala

### Maintenance
1. **Test koneksi** secara berkala
2. **Update router status** jika ada perubahan
3. **Monitor error logs** untuk masalah koneksi
4. **Backup router config** secara terpisah

## 🔗 Related Documentation

- **Database Schema**: Lihat tabel `mikrotiks` dan field MikroTik di `pelanggans`
- **API Documentation**: RouterOS API documentation
- **Routes**: `/mikrotiks/*` routes di `routes/web.php`
- **Service**: `app/Services/MikrotikService.php`

## 📞 Support

Jika mengalami masalah dengan integrasi MikroTik:
1. Cek troubleshooting section di atas
2. Review error logs di `storage/logs/laravel.log`
3. Test koneksi manual di dashboard router
4. Pastikan RouterOS API aktif dan dapat diakses

---

**Last Updated**: November 2024
**Version**: 1.0.0

