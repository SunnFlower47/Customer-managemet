# 📚 Tutorial Penggunaan Sistem Utama WiFi Management

## 🎯 **Overview**
Sistem WiFi Management adalah aplikasi web untuk mengelola pelanggan, pembayaran, paket, dan laporan bisnis WiFi. Tutorial ini akan memandu Anda menggunakan semua fitur utama sistem.

---

## 🔐 **Login & Dashboard**

### **Login ke Sistem**
1. Buka browser dan akses URL sistem
2. Masukkan **Username** dan **Password**
3. Klik **Login**

### **Dashboard Utama**
- **Statistik**: Total pelanggan, pembayaran, dan pendapatan
- **Menu Navigasi**: Sidebar kiri untuk akses semua fitur
- **Quick Actions**: Tombol cepat untuk fitur yang sering digunakan

---

## 👥 **Manajemen Pelanggan**

### **1. Melihat Daftar Pelanggan**
1. Klik menu **"Pelanggan"** di sidebar
2. Lihat daftar semua pelanggan dengan informasi:
   - Nama pelanggan
   - PPPoE username
   - Serial Number STB
   - No HP
   - Alamat
   - Status (Aktif/Isolir/Bayar Double)

### **2. Mencari Pelanggan**
- Gunakan **search box** di atas tabel
- Bisa mencari berdasarkan:
  - Nama pelanggan
  - PPPoE username
  - Serial Number STB
  - No HP
  - Alamat

### **3. Menambah Pelanggan Baru**
1. Klik tombol **"Tambah Pelanggan"**
2. Isi form dengan data:
   - **Nama**: Nama lengkap pelanggan
   - **PPPoE**: Username untuk login WiFi
   - **Serial Number STB**: Nomor seri perangkat STB
   - **Alamat**: Alamat lengkap pelanggan
   - **No HP**: Nomor telepon pelanggan
   - **Paket**: Pilih paket internet
   - **Tanggal Mulai**: Tanggal mulai berlangganan
   - **Tanggal Pembayaran**: Tanggal jatuh tempo setiap bulan
   - **Penagih**: Pilih penagih (opsional)
   - **Status**: Aktif/Isolir/Bayar Double
3. Klik **"Simpan"**

> **💡 Tips**: Password default untuk pelanggan baru adalah **123456**

### **4. Edit Pelanggan**
1. Klik tombol **"Edit"** pada pelanggan yang ingin diubah
2. Ubah data yang diperlukan
3. Klik **"Update"**

### **5. Hapus Pelanggan**
1. Klik tombol **"Hapus"** pada pelanggan
2. Konfirmasi penghapusan
3. Pelanggan akan dihapus dari sistem

---

## 💰 **Manajemen Pembayaran**

### **1. Melihat Daftar Pembayaran**
1. Klik menu **"Pembayaran"** di sidebar
2. Lihat daftar pembayaran dengan informasi:
   - Kode pembayaran
   - Nama pelanggan
   - Paket
   - Nominal
   - Status (Lunas/Belum Lunas)
   - Tanggal jatuh tempo

### **2. Mencari Pembayaran**
- Gunakan **filter** berdasarkan:
  - Status pembayaran
  - Tanggal
  - Nama pelanggan

### **3. Input Pembayaran**
1. Klik tombol **"Input Pembayaran"**
2. Pilih pelanggan
3. Isi data pembayaran:
   - Nominal
   - Tanggal pembayaran
   - Metode pembayaran
   - Catatan (opsional)
4. Klik **"Simpan"**

### **4. Upload Bukti Pembayaran**
1. Klik tombol **"Upload Bukti"** pada pembayaran
2. Pilih file gambar bukti pembayaran
3. Klik **"Upload"**

---

## 📦 **Manajemen Paket**

### **1. Melihat Daftar Paket**
1. Klik menu **"Paket"** di sidebar
2. Lihat daftar paket dengan informasi:
   - Nama paket
   - Kecepatan
   - Harga
   - Status

### **2. Menambah Paket Baru**
1. Klik tombol **"Tambah Paket"**
2. Isi form:
   - **Nama Paket**: Nama paket internet
   - **Kecepatan**: Kecepatan internet (Mbps)
   - **Harga**: Harga bulanan
   - **Deskripsi**: Deskripsi paket
3. Klik **"Simpan"**

### **3. Edit/Hapus Paket**
- Klik **"Edit"** untuk mengubah paket
- Klik **"Hapus"** untuk menghapus paket

---

## 👨‍💼 **Manajemen Penagih**

### **1. Melihat Daftar Penagih**
1. Klik menu **"Penagih"** di sidebar
2. Lihat daftar penagih dengan informasi:
   - Nama penagih
   - No HP
   - Alamat
   - Jumlah pelanggan

### **2. Menambah Penagih Baru**
1. Klik tombol **"Tambah Penagih"**
2. Isi form:
   - **Nama**: Nama lengkap penagih
   - **No HP**: Nomor telepon
   - **Alamat**: Alamat penagih
3. Klik **"Simpan"**

---

## 📊 **Laporan & Analytics**

### **1. Laporan Pembayaran**
1. Klik menu **"Laporan"** → **"Pembayaran"**
2. Pilih periode laporan
3. Klik **"Generate"** untuk melihat laporan
4. Klik **"Export"** untuk download PDF/Excel

### **2. Laporan Pelanggan**
1. Klik menu **"Laporan"** → **"Pelanggan"**
2. Pilih filter yang diinginkan
3. Klik **"Generate"** untuk melihat laporan

### **3. Laporan Keuangan**
1. Klik menu **"Laporan"** → **"Keuangan"**
2. Pilih periode
3. Lihat ringkasan pendapatan dan pengeluaran

---

## ⚙️ **Pengaturan Sistem**

### **1. Profil Akun**
1. Klik menu **"Pengaturan"** → **"Profil Akun"**
2. Ubah data profil:
   - Nama lengkap
   - Email
   - Password
3. Klik **"Simpan Perubahan"**

### **2. Backup Database**
1. Klik menu **"Pengaturan"** → **"Backup Database"**
2. Klik **"Buat Backup Database"**
3. File backup akan otomatis terdownload

### **3. Role & Permission**
1. Klik menu **"Pengaturan"** → **"Role & Permission"**
2. Kelola role dan permission user
3. Buat role baru atau edit role existing

### **4. Profil Perusahaan**
1. Klik menu **"Pengaturan"** → **"Profil Perusahaan"**
2. Isi data perusahaan:
   - Nama perusahaan
   - Alamat
   - Kontak
   - Logo perusahaan
3. Klik **"Simpan"**

---

## 🔍 **Tips & Shortcuts**

### **Keyboard Shortcuts**
- **Ctrl + F**: Cari di halaman
- **Ctrl + S**: Simpan form
- **Esc**: Tutup modal

### **Tips Efisiensi**
1. **Gunakan Search**: Cari pelanggan/pembayaran dengan cepat
2. **Filter Data**: Gunakan filter untuk melihat data spesifik
3. **Export Laporan**: Download laporan untuk analisis offline
4. **Backup Rutin**: Lakukan backup database secara berkala

### **Troubleshooting**
- **Lupa Password**: Hubungi admin untuk reset
- **Data Tidak Muncul**: Refresh halaman atau cek koneksi internet
- **Error Upload**: Pastikan file tidak terlalu besar (max 5MB)

---

## 📱 **Mobile Responsive**

### **Akses dari Mobile**
- Sistem dapat diakses dari smartphone/tablet
- Layout otomatis menyesuaikan ukuran layar
- Touch-friendly untuk navigasi

### **Fitur Mobile**
- **Sidebar Collapsible**: Sidebar dapat disembunyikan
- **Touch Navigation**: Navigasi dengan touch gesture
- **Responsive Tables**: Tabel otomatis menyesuaikan layar

---

## 🆘 **Bantuan & Support**

### **Kontak Support**
- **Email**: support@company.com
- **WhatsApp**: +62-xxx-xxx-xxxx
- **Telepon**: +62-xxx-xxx-xxxx

### **FAQ Umum**
1. **Q: Bagaimana cara reset password?**
   - A: Hubungi admin untuk reset password

2. **Q: Data pelanggan tidak muncul?**
   - A: Cek koneksi internet dan refresh halaman

3. **Q: Bagaimana cara backup data?**
   - A: Pergi ke Pengaturan → Backup Database

4. **Q: Bisa akses dari mobile?**
   - A: Ya, sistem responsive untuk semua device

---

## 📋 **Checklist Harian**

### **Rutinitas Harian**
- [ ] Cek pembayaran yang jatuh tempo
- [ ] Input pembayaran baru
- [ ] Update status pelanggan
- [ ] Cek laporan harian

### **Rutinitas Mingguan**
- [ ] Generate laporan mingguan
- [ ] Backup database
- [ ] Review performa sistem
- [ ] Update data pelanggan

### **Rutinitas Bulanan**
- [ ] Generate laporan bulanan
- [ ] Analisis performa bisnis
- [ ] Update paket dan harga
- [ ] Review dan update sistem

---

**📅 Last Updated**: $(date)  
**👤 Created By**: AI Assistant  
**🎯 Purpose**: User Guide untuk Sistem Utama WiFi Management
