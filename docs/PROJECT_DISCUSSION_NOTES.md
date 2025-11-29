# 📋 Project Discussion Notes - WiFi Billing Management System

## 🎯 Overview
Sistem manajemen billing WiFi dengan fitur customer self-service, API integration, dan WhatsApp automation.

## 🔐 Security & GitHub Strategy

### Files yang di-ignore untuk GitHub:
```
# API Controllers - Sensitive Information
/app/Http/Controllers/Api/
/routes/api.php
/config/api.php
/docs/API_SECURITY_GUIDE.md
/docs/API_DOCUMENTATION.md
/API_DOCUMENTATION.md

# Test files and data - Sensitive Information
/test/
/tests/
/test_*.php
*_test.php
/test-data/
/sample-data/
/database/seeders/test/
/storage/app/test/
/storage/logs/test/

# Root folder files - Sensitive Information
/TESTING_REPORT.md
/COMPREHENSIVE_AUDIT_REPORT.md
/pelanggan.xlsx
*.xlsx
*.xls

# Public folder - Contains assets and sensitive files
/public/
```

### Files yang tetap di-commit:
- ✅ `README.md` - Dokumentasi utama
- ✅ Semua file aplikasi utama (controllers, models, views)
- ✅ Database migrations
- ✅ Konfigurasi umum

## 🚀 Customer Login System Implementation

### Database Migration:
```php
// 2025_01_10_000000_add_customer_auth_fields_to_pelanggans_table.php
Schema::table('pelanggans', function (Blueprint $table) {
    $table->string('password')->nullable()->after('no_hp');
    $table->string('remember_token', 100)->nullable()->after('password');
    $table->timestamp('last_login_at')->nullable()->after('remember_token');
    $table->boolean('is_default_password')->default(true)->after('last_login_at');
});
```

### Login Strategy:
- **Login dengan No HP + Password** (bukan PPPoE)
- **Auto-update credential** ketika no HP berubah di database
- **Generate password default** untuk semua pelanggan existing
- **Notifikasi via WhatsApp** ketika no HP berubah

### Flow Implementation:
1. **Admin buat pelanggan baru** → Generate password default
2. **Kirim password via WhatsApp** → Pelanggan dapat akses (TODO: implementasi WhatsApp nanti)
3. **Pelanggan login pertama kali** → Sistem minta ganti password
4. **Pelanggan ganti password** → Bisa akses semua fitur
5. **Pelanggan cek tagihan** → Self-service

## 🔌 API Endpoints Strategy

### ⚠️ IMPORTANT: Semua API Customer Harus Protected (Private)
**Semua API untuk customer harus menggunakan authentication karena pelanggan harus login dulu.**

### Public API (Tanpa Authentication) - Minimal:
```
GET  /api/v1/health                     # Health check
```

### Protected API - Customer (Dengan Authentication):
```
POST /api/v1/customer/login             # Login pelanggan
GET  /api/v1/customer/bills             # Cek tagihan (setelah login)
GET  /api/v1/customer/payment-history   # Riwayat pembayaran
PUT  /api/v1/customer/profile           # Update profil
POST /api/v1/customer/change-password   # Ganti password
GET  /api/v1/customer/logout            # Logout
```

### Protected API - Admin (Dengan Authentication):
```
GET  /api/v1/admin/customers            # Manajemen pelanggan
GET  /api/v1/admin/payments             # Manajemen pembayaran
GET  /api/v1/admin/packages             # Manajemen paket
GET  /api/v1/admin/dashboard/statistics # Statistik dashboard
GET  /api/v1/admin/reports/revenue      # Laporan pendapatan
GET  /api/v1/admin/users                # Manajemen user
```

### ⚠️ WhatsApp API - BELUM IMPLEMENTASI:
```
# WhatsApp endpoints belum siap karena belum ada API WhatsApp
# Akan diimplementasikan nanti setelah ada API WhatsApp
POST /api/v1/whatsapp/send-payment-proof # TODO: Kirim bukti pembayaran ke admin
POST /api/v1/whatsapp/send-reminder      # TODO: Kirim reminder tagihan
GET  /api/v1/whatsapp/status/{id}        # TODO: Status pesan WhatsApp
```

## 📱 Customer Self-Service Features

### 🎯 Key Features - Customer Portal:
- ✅ **Login dengan No HP + Password**
- ✅ **Cek tagihan** - Lihat tagihan yang belum dibayar
- ✅ **Informasi pelanggan** - Lihat data pribadi (nama, alamat, paket)
- ✅ **Pembayaran manual** - Upload bukti pembayaran
- ✅ **Riwayat pembayaran** - Lihat history pembayaran
- ✅ **Update profil** - Edit nama, alamat
- ✅ **Ganti password** - Ubah password default

### 💳 Payment Method - Manual (Bukan Payment Gateway):
- ✅ **Metode pembayaran manual** - DANA, OVO, Transfer Bank, dll
- ✅ **Nomor rekening/wallet** - Tampilkan nomor tujuan pembayaran
- ✅ **Dual Payment Proof System** - 2 cara kirim bukti pembayaran
- ❌ **Tidak pakai Payment Gateway** - Terlalu rumit, pakai manual saja

### 📤 Dual Payment Proof System:
#### **Cara 1: Upload di Website**
- ✅ Customer upload bukti pembayaran di website
- ✅ Sistem otomatis kirim ke WhatsApp admin
- ✅ Admin dapat notifikasi langsung
- ✅ Sistem kirim konfirmasi ke pelanggan setelah verifikasi

#### **Cara 2: Kirim Manual ke WhatsApp Admin**
- ✅ **Tombol "Kirim ke WhatsApp"** dengan nomor admin tertera
- ✅ **Redirect ke WhatsApp** dengan pesan template
- ✅ Customer kirim manual via WhatsApp
- ✅ Admin verifikasi manual → Update status di sistem

### API Endpoints untuk Customer (Protected):
```
POST /api/v1/customer/login             # Login pelanggan
GET  /api/v1/customer/bills             # Cek tagihan (setelah login)
GET  /api/v1/customer/info              # Informasi pelanggan (setelah login)
GET  /api/v1/customer/payment-methods   # Metode pembayaran (DANA, OVO, dll)
POST /api/v1/customer/upload-payment    # Upload bukti pembayaran (Cara 1)
GET  /api/v1/customer/whatsapp-link     # Link WhatsApp admin (Cara 2)
GET  /api/v1/customer/payment-proofs    # Riwayat bukti pembayaran
GET  /api/v1/customer/payment-history   # Riwayat pembayaran (setelah login)
PUT  /api/v1/customer/profile           # Update profil (setelah login)
POST /api/v1/customer/change-password   # Ganti password (setelah login)
GET  /api/v1/customer/logout            # Logout
```

## 🔒 Security Considerations

### Keamanan yang Diperlukan:
1. **Rate Limiting** - Max 10 request per menit per IP
2. **Input Validation** - Validasi semua input
3. **Logging** - Log semua aktivitas login
4. **Data Filtering** - Hanya tampilkan info yang perlu
5. **API Keys** - Untuk WhatsApp dan Payment Gateway

### Masalah Keamanan yang Ditemukan:
- ⚠️ **Payment API tidak ada autentikasi** - Siapa saja bisa cek status
- ⚠️ **WhatsApp API belum implementasi** - Belum ada API WhatsApp
- ⚠️ **Tidak ada rate limiting** - Bisa di-spam
- ⚠️ **Tidak ada logging** - Sulit track abuse

## 🚀 Deployment Strategy

### Testing Flow:
1. **Local** → Test migration dan fitur
2. **Development** → Test dengan data real
3. **Production** → Deploy setelah aman

### Migration Safety:
- ✅ **Data existing tidak hilang** - hanya tambah field
- ✅ **Sistem tetap jalan** - tidak ada downtime
- ✅ **Rollback mudah** - bisa undo migration
- ✅ **Generate password otomatis** - untuk data existing

## 📋 Implementation Checklist

### Phase 1: Database & Migration
- [ ] Copy migration file ke development
- [ ] Test migration di development
- [ ] Generate password untuk data existing
- [ ] Test customer login

### Phase 2: API Security
- [ ] Konfigurasi API keys
- [ ] Implementasi rate limiting
- [ ] Tambahkan logging
- [ ] Test semua endpoints

### Phase 3: Customer Portal
- [ ] Buat halaman customer login
- [ ] Implementasi cek tagihan
- [ ] Implementasi ganti password
- [ ] Test WhatsApp integration

### Phase 4: Production Deploy
- [ ] Backup database production
- [ ] Deploy migration ke production
- [ ] Generate password untuk data existing
- [ ] Monitor sistem

## 🔧 Technical Notes

### Database Schema:
```sql
pelanggans
├── id (Primary Key)
├── nama, pppoe, alamat, no_hp (existing)
├── paket_id, penagih_id, status (existing)
├── password (NEW - untuk login)
├── remember_token (NEW - untuk session)
├── last_login_at (NEW - tracking login)
├── is_default_password (NEW - flag ganti password)
└── timestamps (existing)

payment_proofs (NEW TABLE)
├── id (Primary Key)
├── pembayaran_id (Foreign Key → pembayarans)
├── pelanggan_id (Foreign Key → pelanggans)
├── file_path (Path file bukti)
├── file_name (Nama file asli)
├── file_type (image/jpeg, image/png)
├── file_size (Size file dalam bytes)
├── status (pending/verified/rejected)
├── admin_notes (Catatan admin)
├── verified_by (Admin yang verifikasi)
├── verified_at (Waktu verifikasi)
├── submission_method (website_upload/whatsapp_manual)
├── whatsapp_message_id (ID pesan WhatsApp)
└── timestamps
```

### Key Files:
- `routes/api.php` - API routes
- `app/Http/Controllers/Api/` - API controllers
- `database/migrations/2025_01_10_000000_add_customer_auth_fields_to_pelanggans_table.php` - Migration customer auth
- `database/migrations/2025_01_10_000001_create_payment_proofs_table.php` - Migration payment proofs
- `.gitignore` - Security configuration

## 📞 Next Steps

1. **Test migration di development** dengan data existing
2. **Implementasi customer login system**
3. **Konfigurasi API security**
4. **Buat customer portal**
5. **Deploy ke production**

## 🎯 Goals

- ✅ **Customer self-service** - Pelanggan bisa cek tagihan sendiri
- ✅ **API ready** - Siap untuk payment gateway integration
- ✅ **WhatsApp automation** - Kirim notifikasi otomatis
- ✅ **Security** - Data sensitif tidak masuk GitHub
- ✅ **Scalable** - Sistem bisa dikembangkan lebih lanjut

---

**Last Updated:** January 10, 2025  
**Status:** Ready for Implementation  
**Priority:** High
