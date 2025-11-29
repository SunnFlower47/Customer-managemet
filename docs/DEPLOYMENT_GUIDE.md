# 🚀 **DEPLOYMENT GUIDE - WiFi Management System**

## 📋 **OVERVIEW**
Panduan lengkap untuk deployment sistem WiFi Management ke hosting production.

---

## 🔧 **DEPENDENCIES BARU YANG PERLU DIINSTALL**

### **Composer Dependencies**
Dependencies berikut **BARU** ditambahkan untuk API Customer Portal:

```json
{
    "laravel/sanctum": "^4.2",           // API Authentication
    "tymon/jwt-auth": "^2.2",           // JWT Token Authentication  
    "spatie/laravel-permission": "^6.21" // Role & Permission System
}
```

### **Dependencies yang Sudah Ada**
```json
{
    "barryvdh/laravel-dompdf": "^3.1",  // PDF Export
    "maatwebsite/excel": "^3.1"         // Excel Import/Export
}
```

---

## 🚀 **LANGKAH DEPLOYMENT**

### **1. Upload Files**
Upload semua file project ke hosting, termasuk:
- `composer.json` (dengan dependencies baru)
- `composer.lock` 
- **PENTING: Upload semua file routes:**
  - `routes/web.php`
  - `routes/api.php` ← **File ini yang sering terlewat!**
  - `routes/console.php`
- Semua file aplikasi lainnya

### **2. Install Dependencies**
```bash
# Install semua dependencies (termasuk yang baru)
composer install --no-dev --optimize-autoloader

# Regenerate autoload (PENTING!)
composer dump-autoload

# Cek apakah Sanctum terinstall
composer show laravel/sanctum

# Publish Sanctum (jika belum ada)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations"
```

**Note:** Spatie Permission sudah ada dan tidak perlu di-publish lagi.

### **3. Service Provider Setup (PENTING!)**
**Edit file `bootstrap/providers.php` di hosting:**

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,  // TAMBAHKAN INI
];
```

**Jika tidak ditambahkan, akan error: "Trait HasApiTokens not found"**

### **4. Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

### **4. Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed permissions dan data essential
php artisan db:seed

# Atau seed individual (jika perlu):
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=TicketPaymentProofPermissionSeeder
php artisan db:seed --class=CompanyProfileSeeder
```

### **5. Clear Cache**
```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear
```

### **6. Storage Setup**
```bash
# Create storage link
php artisan storage:link

# Set permissions (jika perlu)
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

## 🔐 **ENVIRONMENT VARIABLES**

Pastikan `.env` memiliki konfigurasi berikut:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=your_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Configuration
JWT_SECRET=your_jwt_secret
JWT_TTL=60

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=your_domain.com
SESSION_DOMAIN=your_domain.com

# File Upload
FILESYSTEM_DISK=public
```

---

## 📱 **CUSTOMER PORTAL DEPLOYMENT**

### **Frontend Build**
```bash
cd customer-portal
npm install
npm run build
```

### **API Endpoints**
Pastikan API endpoints dapat diakses:
- `POST /api/v1/customer/auth/login`
- `GET /api/v1/customer/profile`
- `GET /api/v1/customer/payments`
- `POST /api/v1/customer/payments/upload-proof`
- `GET /api/v1/customer/tickets`
- `POST /api/v1/customer/tickets`

---

## ✅ **POST-DEPLOYMENT CHECKLIST**

### **File Upload Checklist**
- [ ] `composer.json` dan `composer.lock` ter-upload
- [ ] `routes/web.php` ter-upload
- [ ] `routes/api.php` ter-upload ← **PENTING!**
- [ ] `routes/console.php` ter-upload
- [ ] `bootstrap/providers.php` ter-upload (dengan SanctumServiceProvider)
- [ ] `config/cors.php` ter-upload ← **BARU! Untuk fix 403 frontend**
- [ ] Semua file aplikasi ter-upload

### **Functionality Checklist**
- [ ] Main system login berfungsi
- [ ] Customer portal login berfungsi (password: 123456)
- [ ] API endpoints dapat diakses
- [ ] File upload berfungsi
- [ ] PDF export berfungsi
- [ ] Excel import/export berfungsi
- [ ] Permission system berfungsi
- [ ] Database migrations berhasil
- [ ] Database seeders berhasil (RolePermission, TicketPaymentProof, CompanyProfile)
- [ ] Storage link terbuat
- [ ] Cache cleared

---

## 🐛 **TROUBLESHOOTING**

### **Error: Class "Laravel\Sanctum\Sanctum" not found**
**SOLUSI:**
1. **Cek apakah Sanctum terinstall:**
   ```bash
   composer show laravel/sanctum
   ```
2. **Jika belum terinstall, install ulang:**
   ```bash
   composer require laravel/sanctum
   ```
3. **Regenerate autoload:**
   ```bash
   composer dump-autoload
   ```
4. **Edit file `bootstrap/providers.php`** - tambahkan `Laravel\Sanctum\SanctumServiceProvider::class`
5. **Publish Sanctum:**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations"
   ```
6. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### **Error: Trait "Laravel\Sanctum\HasApiTokens" not found**
**SOLUSI:**
1. **Edit file `bootstrap/providers.php`** - tambahkan `Laravel\Sanctum\SanctumServiceProvider::class`
2. **Regenerate autoload:**
   ```bash
   composer dump-autoload
   ```
3. **Publish Sanctum:**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations"
   ```
4. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### **Error: Class not found**
```bash
composer dump-autoload
```

### **Error: Permission denied**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### **Error: JWT secret not set**
```bash
php artisan jwt:secret
```

### **Error: No publishable resources for tag []**
```bash
# Cek tag yang tersedia
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Publish dengan tag yang benar
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations"
```

### **Error: Sanctum not configured**
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations"
```

### **Error: require(/path/to/routes/api.php): Failed to open stream**
**SOLUSI:**
1. **Pastikan file `routes/api.php` ter-upload ke hosting**
2. **Cek apakah file ada:**
   ```bash
   ls -la routes/api.php
   ```
3. **Jika tidak ada, upload file `routes/api.php` dari project lokal**
4. **Set permission yang benar:**
   ```bash
   chmod 644 routes/api.php
   ```

### **Error: 403 Forbidden - Access to this resource on the server is denied**
**SOLUSI:**
1. **Set permission file yang benar:**
   ```bash
   # Set permission untuk folder dan file
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   chmod -R 644 public
   chmod 644 .env
   chmod 644 public/.htaccess
   ```

2. **Cek file .htaccess di public:**
   ```bash
   ls -la public/.htaccess
   ```

3. **Pastikan document root mengarah ke folder `public`:**
   - **Apache:** DocumentRoot harus mengarah ke `/path/to/project/public`
   - **Nginx:** root harus mengarah ke `/path/to/project/public`

4. **Cek konfigurasi web server:**
   - **Apache:** Pastikan mod_rewrite enabled
   - **Nginx:** Pastikan try_files directive ada

5. **Test akses file index.php:**
   ```bash
   # Coba akses langsung
   curl http://your-domain.com/index.php
   ```

### **Error: 403 Forbidden di Staging Server**
**SOLUSI KHUSUS STAGING:**
1. **Cek permission file dan folder:**
   ```bash
   # Set permission yang benar untuk staging
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   chmod -R 644 public
   chmod 644 .env
   chmod 644 public/.htaccess
   chmod 644 routes/api.php
   ```

2. **Cek file `.htaccess` di public:**
   ```bash
   ls -la public/.htaccess
   cat public/.htaccess
   ```

3. **Cek konfigurasi web server staging:**
   ```bash
   # Cek apakah mod_rewrite enabled
   apache2ctl -M | grep rewrite
   
   # Atau untuk nginx
   nginx -t
   ```

4. **Test akses langsung ke index.php:**
   ```bash
   curl https://developmet.barayacitramandiri.net/index.php
   ```

5. **Cek document root staging:**
   ```bash
   # Pastikan document root mengarah ke folder public
   pwd
   ls -la public/
   ```

6. **Cek file `.env` untuk staging:**
   ```env
   APP_ENV=staging
   APP_DEBUG=true
   SANCTUM_STATEFUL_DOMAINS=developmet.barayacitramandiri.net
   SESSION_DOMAIN=developmet.barayacitramandiri.net
   ```

7. **Test API endpoint dengan index.php:**
   ```bash
   curl https://developmet.barayacitramandiri.net/index.php/api/v1/health
   ```

### **Error: 403 Forbidden di Frontend (Customer Portal)**
**SOLUSI:**
1. **Cek CORS configuration di `config/cors.php`:**
   ```php
   'allowed_origins' => ['*'], // atau domain frontend Anda
   'allowed_methods' => ['*'],
   'allowed_headers' => ['*'],
   ```

2. **Cek middleware CORS di `bootstrap/app.php`:**
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->api(prepend: [
           \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
       ]);
   })
   ```

3. **Test API endpoint langsung:**
   ```bash
   # Test health check
   curl https://developmet.barayacitramandiri.net/api/v1/health
   
   # Test login endpoint
   curl -X POST https://developmet.barayacitramandiri.net/api/v1/customer/auth/login \
     -H "Content-Type: application/json" \
     -d '{"no_hp":"081234567890","password":"123456"}'
   ```

4. **Cek file `.env` untuk CORS:**
   ```env
   SANCTUM_STATEFUL_DOMAINS=developmet.barayacitramandiri.net
   SESSION_DOMAIN=developmet.barayacitramandiri.net
   ```

5. **Pastikan frontend menggunakan URL yang benar:**
   ```javascript
   // Di customer-portal/src/config/api.ts
   BASE_URL: 'https://developmet.barayacitramandiri.net/api/v1'
   ```

6. **Test API endpoint dengan domain yang benar:**
   ```bash
   # Test health check
   curl https://developmet.barayacitramandiri.net/api/v1/health
   
   # Test login endpoint
   curl -X POST https://developmet.barayacitramandiri.net/api/v1/customer/auth/login \
     -H "Content-Type: application/json" \
     -d '{"no_hp":"081234567890","password":"123456"}'
   ```

### **Error: "This Page Does Not Exist" - API endpoint tidak ditemukan**
**SOLUSI:**
1. **✅ File `routes/api.php` sudah ada** (28830 bytes, Sep 15 15:41)

2. **Cek apakah file `public/index.php` ada:**
   ```bash
   ls -la public/index.php
   ```

3. **Cek file `.htaccess` di public:**
   ```bash
   ls -la public/.htaccess
   ```

4. **Test akses langsung ke index.php:**
   ```bash
   curl https://developmet.barayacitramandiri.net/index.php
   ```

5. **Test route API dengan index.php:**
   ```bash
   curl https://developmet.barayacitramandiri.net/index.php/api/v1/health
   ```

6. **Cek konfigurasi web server:**
   - **Apache:** Pastikan mod_rewrite enabled
   - **Nginx:** Pastikan try_files directive ada

7. **Pastikan document root mengarah ke folder `public`:**
   - Document root harus: `/path/to/project/public`
   - Bukan: `/path/to/project`

8. **Test route Laravel:**
   ```bash
   # Test route web
   curl https://developmet.barayacitramandiri.net/
   ```

### **Error: Permission tables not found**
```bash
# Cek apakah migration permission sudah ada
php artisan migrate:status

# Jika belum, run migration
php artisan migrate
```

---

## 📞 **SUPPORT**

Jika ada masalah deployment, cek:
1. PHP version >= 8.2
2. Composer dependencies terinstall
3. Database connection berfungsi
4. File permissions benar
5. Environment variables lengkap

---

**Last Updated:** $(date)
**Version:** 1.0.0
