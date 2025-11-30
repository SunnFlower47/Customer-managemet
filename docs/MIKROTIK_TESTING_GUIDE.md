# 🧪 MikroTik Testing Guide

Panduan lengkap untuk testing fitur MikroTik tanpa perangkat fisik.

## 📋 Daftar Isi

1. [Mode Testing (Mock Data)](#mode-testing-mock-data)
2. [MikroTik CHR di VPS/Cloud](#mikrotik-chr-di-vpscloud)
3. [MikroTik CHR di Docker](#mikrotik-chr-di-docker)
4. [Menggunakan MikroTik di Cloud](#menggunakan-mikrotik-di-cloud)

---

## 🎯 Mode Testing (Mock Data)

Cara termudah untuk testing tanpa perangkat fisik adalah menggunakan **Testing Mode** yang sudah disediakan.

### Cara Mengaktifkan Testing Mode

1. **Buka file `.env`** di root folder `backend/`

2. **Tambahkan konfigurasi berikut:**
```env
MIKROTIK_TESTING_MODE=true
```

3. **Clear config cache** (jika perlu):
```bash
php artisan config:clear
```

### Fitur yang Tersedia di Testing Mode

✅ **Test Connection** - Selalu berhasil dengan mock identity "Test Router"  
✅ **Resource Usage** - Menampilkan data mock (CPU, Memory, Uptime)  
✅ **Active PPPoE Users** - Menampilkan 2 user mock (testuser1, testuser2)  
✅ **Search PPPoE** - Mencari user mock berdasarkan username  

### Mock Data yang Tersedia

#### PPPoE Users untuk Testing:
- **Username**: `testuser1` → IP: `192.168.1.100`
- **Username**: `testuser2` → IP: `192.168.1.101`

#### Resource Usage Mock:
- Uptime: `5d 12h 30m 15s`
- CPU Load: `25%`
- Free Memory: `512M` / Total: `1G`
- Free HDD: `8G` / Total: `16G`

### Cara Menggunakan

1. **Aktifkan testing mode** di `.env`
2. **Tambah MikroTik** di admin panel dengan data apapun:
   - IP Address: `127.0.0.1` atau `192.168.1.1` (tidak akan digunakan)
   - Port: `8728`
   - Username: `admin` (tidak akan digunakan)
   - Password: `password` (tidak akan digunakan)
3. **Test koneksi** - akan selalu berhasil
4. **Cari PPPoE** dengan username `testuser1` atau `testuser2`

### Menonaktifkan Testing Mode

Set `MIKROTIK_TESTING_MODE=false` atau hapus baris tersebut dari `.env`, lalu:
```bash
php artisan config:clear
```

---

## 🖥️ MikroTik CHR di VPS/Cloud

MikroTik CHR (Cloud Hosted Router) adalah versi virtual RouterOS yang bisa diinstall di VPS atau cloud.

### Keuntungan:
- ✅ Gratis untuk testing (trial 60 hari)
- ✅ Bisa diinstall di VPS murah
- ✅ Full RouterOS API support
- ✅ Bisa diakses dari mana saja

### Langkah Instalasi di VPS (Ubuntu/Debian)

#### 1. Download CHR Image
```bash
wget https://download.mikrotik.com/routeros/7.14/chr-7.14.img.zip
unzip chr-7.14.img.zip
```

#### 2. Install di VPS (contoh menggunakan KVM/QEMU)
```bash
# Install QEMU
sudo apt-get update
sudo apt-get install qemu-kvm -y

# Buat disk image
qemu-img create -f qcow2 chr.qcow2 1G

# Boot dari CHR image
qemu-system-x86_64 -hda chr.qcow2 -cdrom chr-7.14.img -boot d -m 256M
```

#### 3. Konfigurasi di Winbox/Terminal
- Login dengan username: `admin`, password: kosong
- Set IP address: `/ip address add address=YOUR_VPS_IP/24 interface=ether1`
- Aktifkan API: `/ip service set api disabled=no port=8728`
- Set password: `/user set admin password=your-password`

#### 4. Tambahkan ke Sistem
- IP Address: IP VPS Anda
- Port: `8728`
- Username: `admin`
- Password: Password yang sudah diset

### Alternatif: Menggunakan Cloud Provider

Beberapa cloud provider menyediakan MikroTik CHR image siap pakai:
- **DigitalOcean**: Marketplace → MikroTik CHR
- **Vultr**: Apps → MikroTik CHR
- **Linode**: Marketplace → MikroTik CHR

---

## 🐳 MikroTik CHR di Docker

Untuk testing lokal, Anda bisa menjalankan MikroTik CHR di Docker.

### Prerequisites
- Docker dan Docker Compose terinstall

### Langkah Instalasi

#### 1. Buat `docker-compose.yml`:
```yaml
version: '3.8'

services:
  mikrotik:
    image: mikrotik/routeros:latest
    container_name: mikrotik-chr
    ports:
      - "8728:8728"  # API port
      - "8729:8729"  # API-SSL port
      - "80:80"      # Web interface
      - "443:443"    # Web interface SSL
    environment:
      - ROS_LICENSE=yes
    cap_add:
      - NET_ADMIN
    network_mode: bridge
```

#### 2. Jalankan:
```bash
docker-compose up -d
```

#### 3. Akses dan Konfigurasi:
- Login via web: `http://localhost` (admin, password kosong)
- Atau via Winbox: `localhost`
- Aktifkan API: `/ip service set api disabled=no port=8728`

#### 4. Tambahkan ke Sistem:
- IP Address: `127.0.0.1` atau IP Docker container
- Port: `8728`
- Username: `admin`
- Password: (set password dulu)

---

## ☁️ Menggunakan MikroTik di Cloud

### Opsi 1: MikroTik Cloud Hosted Router (CHR) Trial

1. **Daftar di MikroTik Cloud** (opsional, untuk extended trial)
2. **Download CHR image** dari [mikrotik.com](https://mikrotik.com/download)
3. **Deploy di cloud provider** (AWS, GCP, Azure, dll)
4. **Konfigurasi** seperti langkah di atas

### Opsi 2: Menggunakan Router MikroTik yang Sudah Ada

Jika Anda memiliki akses ke router MikroTik yang sudah terpasang:

1. **Pastikan API aktif:**
```bash
/ip service
set api disabled=no port=8728
```

2. **Buat user API** (opsional, lebih aman):
```bash
/user add name=api-user password=secure-password group=full
```

3. **Buka firewall** untuk IP server Anda:
```bash
/ip firewall filter
add chain=input protocol=tcp dst-port=8728 src-address=YOUR_SERVER_IP action=accept
```

4. **Tambahkan ke sistem** dengan IP public router

---

## 🔧 Troubleshooting

### Testing Mode tidak bekerja
- Pastikan `MIKROTIK_TESTING_MODE=true` di `.env`
- Clear config: `php artisan config:clear`
- Restart server jika perlu

### Koneksi ke CHR gagal
- Pastikan API service aktif: `/ip service print where name=api`
- Cek firewall rules
- Pastikan port 8728 terbuka
- Cek IP address dan port yang benar

### PPPoE tidak ditemukan
- Pastikan user sudah dibuat di router: `/ppp secret print`
- Cek username spelling (case sensitive)
- Di testing mode, gunakan `testuser1` atau `testuser2`

---

## 📝 Catatan Penting

⚠️ **Testing Mode hanya untuk development/testing**  
⚠️ **Jangan aktifkan testing mode di production**  
⚠️ **CHR trial berlaku 60 hari**, setelah itu perlu license  
⚠️ **Pastikan firewall router mengizinkan koneksi dari server**

---

## 🎓 Tips Testing

1. **Gunakan Testing Mode** untuk development cepat
2. **Gunakan CHR di Docker** untuk testing lebih realistis
3. **Gunakan CHR di VPS** untuk testing production-like environment
4. **Test semua fitur** sebelum deploy ke production

---

## 📚 Referensi

- [MikroTik RouterOS API Documentation](https://wiki.mikrotik.com/wiki/Manual:API)
- [MikroTik CHR Documentation](https://help.mikrotik.com/docs/display/ROS/CHR)
- [Docker Hub - MikroTik RouterOS](https://hub.docker.com/r/mikrotik/routeros)

