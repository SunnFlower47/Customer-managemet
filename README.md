# 🌐 WiFi Billing Management System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)]()
[![Version](https://img.shields.io/badge/Version-4.0.0-blue.svg)]()

> **Professional WiFi billing management system with immutable data integrity, audit trails, mobile-responsive design, MikroTik router integration, and location mapping.**

## 📋 Table of Contents

- [✨ Features](#-features)
- [🏗️ Architecture](#️-architecture)
- [🚀 Quick Start](#-quick-start)
- [📊 Database Schema](#-database-schema)
- [🔒 Security & Data Integrity](#-security--data-integrity)
- [📱 Mobile & PWA](#-mobile--pwa)
- [🛠️ API Documentation](#️-api-documentation)
- [📈 Performance](#-performance)
- [🔧 Commands & Scheduler](#-commands--scheduler)
- [🛡️ Security Features](#️-security-features)
- [📊 Audit Trail System](#-audit-trail-system)
- [💾 Backup & Restore](#-backup--restore)
- [🎨 UI/UX Features](#-uiux-features)
- [🚨 Troubleshooting](#-troubleshooting)
- [📚 Documentation](#-documentation)
- [🖥️ MikroTik Integration](#️-mikrotik-router-management)
- [🗺️ ODP & Mapping](#-odp--mapping-system)

## ✨ Features

### 🎯 Core Features
- **✅ Smart Bill Generation** - Automatic billing based on customer payment dates
- **✅ Immutable Payment Records** - Historical data integrity guaranteed
- **✅ Role-Based Access Control** - Admin, Penagih, and custom roles
- **✅ Mobile-Responsive Design** - Perfect on all devices
- **✅ Professional Invoice System** - PDF generation with clean layout
- **✅ Real-time Payment Tracking** - Status updates and notifications
- **✅ Comprehensive Audit Trail** - Complete change logging
- **✅ Database Backup & Restore** - Shared hosting compatible
- **✅ Advanced Search & Filtering** - By name, address, status, collector
- **✅ Export Functionality** - CSV, PDF, Excel exports

### 🚀 New Features (Latest Update)
- **✅ Customer Self-Service API** - Complete RESTful API for mobile/web apps
- **✅ Customer Authentication** - Secure login system for customers
- **✅ Ticket System** - Customer support management with comments & attachments
- **✅ Payment Proof Validation** - Upload and verify payment proofs
- **✅ WhatsApp Integration** - Automated payment notifications
- **✅ Admin Ticket Management** - Complete ticket handling interface
- **✅ Admin Payment Proof Management** - Verification workflow interface
- **✅ File Upload System** - Secure file handling for proofs and attachments
- **✅ Customer Profile Management** - Self-service profile updates
- **✅ Customer Statistics** - Payment and ticket statistics
- **✅ MikroTik Integration** - Router management and PPPoE monitoring
- **✅ ODP Management** - Optical Distribution Point tracking with mapping
- **✅ Location Mapping** - Interactive map with Leaflet.js for customers and ODPs

### 🔐 Security Features
- **✅ Spatie Permission Package** - 50+ granular permissions (including new API permissions)
- **✅ CSRF Protection** - All forms protected
- **✅ Input Validation** - Comprehensive validation rules
- **✅ Rate Limiting** - Prevent abuse and brute force
- **✅ Session Management** - Secure authentication
- **✅ Audit Logging** - All changes tracked
- **✅ Laravel Sanctum** - API token authentication
- **✅ File Upload Security** - Secure file handling with validation
- **✅ API Rate Limiting** - Separate limits for public and authenticated endpoints

### 📱 Mobile & PWA
- **✅ Progressive Web App** - Install as native app
- **✅ Offline Support** - Service worker caching
- **✅ Responsive Design** - Mobile-first approach
- **✅ Touch-Friendly UI** - Optimized for mobile devices
- **✅ Push Notifications** - Payment reminders

## 🏗️ Architecture

### Backend (Laravel 12)
```
├── app/
│   ├── Http/Controllers/Web/     # Web controllers
│   ├── Http/Controllers/Api/     # API controllers
│   ├── Models/                   # Eloquent models
│   ├── Traits/                   # Reusable traits
│   └── Console/Commands/         # Artisan commands
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Data seeders
├── resources/
│   ├── views/                    # Blade templates
│   └── assets/                   # CSS, JS, images
└── routes/
    ├── web.php                   # Web routes
    └── api.php                   # API routes
```

### Technology Stack
- **Framework**: Laravel 12.x with PHP 8.2+
- **Database**: MySQL 8.0+ with optimized indexing
- **Frontend**: Tailwind CSS + Alpine.js
- **Icons**: Font Awesome 6
- **PDF**: DomPDF for invoice generation
- **Permissions**: Spatie Laravel Permission
- **Notifications**: SweetAlert2

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Node.js & NPM (for asset compilation)

### Installation

1. **Clone the repository**
```bash
git clone https:github.com/SunnFlower47/Customer-managemet.git
cd customer-managemet
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database configuration**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wifi_billing
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Compile assets**
```bash
npm run build
```

7. **Start the application**
```bash
php artisan serve
```

### Default Login Credentials
- **Admin**: admin@example.com / password
- **Penagih**: penagih@example.com / password

## 📊 Database Schema

### Core Tables

#### 👥 Users & Roles
```sql
users
├── id (Primary Key)
├── name, email, password
├── role (admin/penagih)
└── timestamps

roles & permissions (Spatie)
├── roles (admin, penagih, custom)
├── permissions (40+ granular permissions)
└── model_has_roles (user-role assignments)
```

#### 🏠 Customers & Packages
```sql
pelanggans (customers)
├── id (Primary Key)
├── nama, pppoe, alamat, no_hp
├── paket_id (Foreign Key → pakets)
├── penagih_id (Foreign Key → penagihs, NULLABLE)
├── status (aktif/isolir/bayar double)
└── timestamps

pakets (packages)
├── id (Primary Key)
├── nama_paket, harga, kecepatan
├── aktif (boolean)
└── timestamps

penagihs (collectors)
├── id (Primary Key)
├── nama, email, no_hp, alamat
├── user_id (Foreign Key → users, NULLABLE)
├── aktif (boolean)
└── timestamps
```

#### 💰 Payments (IMMUTABLE)
```sql
pembayarans (payments)
├── id (Primary Key)
├── kode_pembayaran (Unique)
├── pelanggan_id (Foreign Key → pelanggans)
├── paket_id (Foreign Key → pakets, NULLABLE)
├── nama_paket (Historical - IMMUTABLE)
├── harga_paket (Historical - IMMUTABLE)
├── bulan_tagihan, tahun_tagihan
├── jumlah (IMMUTABLE)
├── status (belum_bayar/lunas)
├── tanggal_bayar (NULLABLE)
├── penagih_id (Foreign Key → penagihs, NULLABLE)
├── nama_penagih (Historical - IMMUTABLE)
├── keterangan
└── timestamps
```

#### 📋 Audit Trail
```sql
audit_trails
├── id (Primary Key)
├── user_id (Foreign Key → users)
├── event (created/updated/deleted)
├── auditable_type (Model class)
├── auditable_id (Model ID)
├── old_values (JSON)
├── new_values (JSON)
├── ip_address, user_agent
├── tags (Custom tags)
└── timestamps
```

#### 🎫 Customer Support System
```sql
tickets
├── id (Primary Key)
├── kode_ticket (Unique)
├── pelanggan_id (Foreign Key → pelanggans)
├── judul, deskripsi
├── kategori (technical/billing/general/complaint)
├── prioritas (low/medium/high/urgent)
├── status (open/in_progress/resolved/closed)
├── assigned_to (Foreign Key → users, NULLABLE)
├── resolved_at (NULLABLE)
├── rating (1-5, NULLABLE)
├── customer_feedback (TEXT, NULLABLE)
└── timestamps

ticket_comments
├── id (Primary Key)
├── ticket_id (Foreign Key → tickets)
├── user_id (Foreign Key → users, NULLABLE)
├── pelanggan_id (Foreign Key → pelanggans, NULLABLE)
├── comment (TEXT)
├── is_internal (BOOLEAN)
└── timestamps

ticket_attachments
├── id (Primary Key)
├── ticket_id (Foreign Key → tickets)
├── filename, file_path, file_type, file_size
├── uploaded_by (Foreign Key → users)
└── timestamps
```

#### 💳 Payment Proof System
```sql
payment_proofs
├── id (Primary Key)
├── pembayaran_id (Foreign Key → pembayarans)
├── pelanggan_id (Foreign Key → pelanggans)
├── file_path, file_name, file_type, file_size
├── status (pending/verified/rejected)
├── admin_notes (TEXT, NULLABLE)
├── verified_by (Foreign Key → users, NULLABLE)
├── verified_at (NULLABLE)
├── submission_method (website_upload/whatsapp)
├── whatsapp_message_id (NULLABLE)
└── timestamps
```

#### 🔐 Customer Authentication
```sql
pelanggans (Updated)
├── password (NULLABLE) - For customer login
├── remember_token (NULLABLE)
├── last_login_at (NULLABLE)
├── is_default_password (BOOLEAN, default: true)
└── [existing fields...]
```

#### 🗺️ Location & Mapping System
```sql
odps (Optical Distribution Points)
├── id (Primary Key)
├── kode_odp (Unique)
├── nama, alamat
├── latitude, longitude
├── kapasitas, port_terpakai
├── status, foto
└── timestamps

pelanggans (Updated with Location)
├── latitude, longitude (NULLABLE)
├── odp_id (Foreign Key → odps, NULLABLE)
└── [existing fields...]
```

#### 🖥️ MikroTik Router Management
```sql
mikrotiks
├── id (Primary Key)
├── nama, ip_address, port
├── username, password (Encrypted)
├── routeros_version (v6/v7/v7.1+)
├── location, description
├── is_active, connection_status
├── last_connected_at, last_error
└── timestamps (with soft deletes)

pelanggans (Updated with MikroTik Integration)
├── mikrotik_id (Foreign Key → mikrotiks, NULLABLE)
├── exists_in_mikrotik (BOOLEAN, NULLABLE)
├── mikrotik_last_checked (TIMESTAMP, NULLABLE)
├── mikrotik_router_name (VARCHAR, NULLABLE)
├── mikrotik_status (VARCHAR, NULLABLE)
├── mikrotik_ip (VARCHAR, NULLABLE)
├── mikrotik_profile (VARCHAR, NULLABLE)
└── [existing fields...]
```

## 🔒 Security & Data Integrity

### 🛡️ Immutable Data Principle

**CRITICAL**: Payment records are **IMMUTABLE** to ensure data integrity and compliance.

#### ✅ What CAN be changed:
- `status` (belum_bayar ↔ lunas)
- `tanggal_bayar` (when payment is made)
- `keterangan` (notes/comments)
- `jumlah` (temporary - for correction only)

#### ❌ What CANNOT be changed:
- `nama_paket` (historical package name)
- `harga_paket` (historical package price)
- `nama_penagih` (historical collector name)
- `bulan_tagihan`, `tahun_tagihan` (billing period)
- `pelanggan_id`, `penagih_id` (relationships)

#### 🔍 Implementation Example:
```php
// ✅ CORRECT - Only allow non-critical updates
public function update(Request $request, Pembayaran $pembayaran)
{
    $request->validate([
        'status' => 'required|in:belum_bayar,lunas',
        'tanggal_bayar' => 'nullable|date',
        'keterangan' => 'nullable|string|max:500'
    ]);

    // Only update allowed fields
    $updateData = $request->only(['status', 'tanggal_bayar', 'keterangan']);
    $pembayaran->update($updateData);
}

// ❌ WRONG - Would violate immutable principle
// $pembayaran->update(['nama_paket' => 'New Package']);
```

### 🔐 Security Features

#### Authentication & Authorization
```php
// Role-based middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Permission-based access
@can('edit-pembayaran')
    <a href="{{ route('pembayarans.edit', $pembayaran) }}">Edit</a>
@endcan
```

#### Input Validation
```php
// Comprehensive validation rules
'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe',
'email' => 'required|email|unique:users,email',
'jumlah' => 'required|numeric|min:0',
'status' => 'required|in:belum_bayar,lunas'
```

## 📱 Mobile & PWA

### 📱 Responsive Design
- **Mobile-first approach** with Tailwind CSS
- **Touch-friendly interfaces** with proper spacing
- **Optimized forms** for mobile input
- **Collapsible navigation** for small screens

### 🔄 PWA Features
- **Service Worker** for offline functionality
- **App manifest** for installation
- **Push notifications** for payment reminders
- **Background sync** for data updates

### 📱 Mobile Optimizations
```css
/* Mobile-specific styles */
@media (max-width: 768px) {
    .mobile-card {
        @apply bg-white rounded-lg shadow p-4 mb-4;
    }
    
    .mobile-button {
        @apply w-full py-3 px-4 text-center;
    }
}
```

## 🛠️ API Documentation

Sistem ini menyediakan **Customer Self-Service API** yang lengkap untuk mobile/web app integration.

### 📚 API Documentation

**Note**: API documentation and endpoints are available for authorized developers only. Contact system administrator for API access and documentation.

## 📈 Performance

### 🚀 Optimization Features

#### Database Indexing
```sql
-- Optimized indexes for performance
CREATE INDEX idx_pembayarans_pelanggan_bulan_tahun 
ON pembayarans(pelanggan_id, bulan_tagihan, tahun_tagihan);

CREATE INDEX idx_pembayarans_status_penagih 
ON pembayarans(status, penagih_id);

CREATE INDEX idx_pelanggans_pppoe 
ON pelanggans(pppoe);
```

#### Query Optimization
```php
// Eager loading to prevent N+1 queries
$pembayarans = Pembayaran::with(['pelanggan', 'penagih'])
    ->where('status', 'belum_bayar')
    ->paginate(10);

// Optimized search with proper indexing
$customers = Pelanggan::where('nama', 'LIKE', "%{$search}%")
    ->orWhere('pppoe', 'LIKE', "%{$search}%")
    ->orWhere('alamat', 'LIKE', "%{$search}%")
    ->paginate(10);
```

#### Caching Strategy
```php
// Cache frequently accessed data
$packages = Cache::remember('packages.active', 3600, function () {
    return Paket::where('aktif', true)->get();
});

// Cache user permissions
$permissions = Cache::remember("user.{$userId}.permissions", 1800, function () use ($userId) {
    return User::find($userId)->getAllPermissions();
});
```

## 🔧 Commands & Scheduler

### 📅 Automated Commands

#### Smart Bills Generation
```bash
# Generate bills for customers with payment date today
php artisan bills:generate-smart

# Generate bills for specific month
php artisan bills:generate-monthly-accurate

# Generate bills for all active customers
php artisan bills:generate-monthly
```

#### Database Operations
```bash
# Create database backup
php artisan backup:database

# Restore database from backup
php artisan restore:database

# Clean old audit trails
php artisan audit:clean --days=90
```

### ⏰ Scheduler Setup
```bash
# Add to crontab for automated tasks
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

#### Scheduled Tasks
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Generate smart bills daily at 6 AM
    $schedule->command('bills:generate-smart')
             ->dailyAt('06:00');
    
    // Clean old audit trails weekly
    $schedule->command('audit:clean --days=90')
             ->weekly();
    
    // Backup database daily at 2 AM
    $schedule->command('backup:database')
             ->dailyAt('02:00');
}
```

## 🛡️ Security Features

### 🔐 Authentication & Authorization

#### Role-Based Access Control
```php
// Available roles
- admin: Full system access
- penagih: Limited to assigned customers
- custom: Custom permissions

// Key permissions
- view-dashboard
- manage-pelanggan
- manage-pembayaran
- manage-paket
- manage-penagih
- view-audit-trail
- manage-users
- backup-database
```

#### Security Middleware
```php
// CSRF protection on all forms
@csrf

// Rate limiting for sensitive operations
Route::middleware(['throttle:restore'])->group(function () {
    Route::post('/settings/restore', [SettingController::class, 'restore']);
});

// Permission-based access
Route::middleware(['permission:edit-pembayaran'])->group(function () {
    Route::get('/pembayarans/{pembayaran}/edit', [PembayaranController::class, 'edit']);
});
```

### 🛡️ Data Protection

#### Input Sanitization
```php
// Automatic XSS protection
{!! $user->name !!}  // ❌ Vulnerable
{{ $user->name }}    // ✅ Safe

// SQL injection prevention
$users = DB::table('users')->where('name', $name)->get(); // ✅ Safe
```

#### File Upload Security
```php
// Secure file upload validation
$request->validate([
    'file' => 'required|file|mimes:sql|max:10240' // Only SQL files, max 10MB
]);
```

## 📊 Audit Trail System

### 🔍 Complete Change Tracking

#### What Gets Logged
- **Customer data changes** (name, address, package, etc.)
- **Payment status updates** (belum_bayar → lunas)
- **Package modifications** (price, name, speed)
- **User management** (role changes, permissions)
- **System settings** (backup, restore operations)

#### Audit Trail Features
```php
// Automatic logging with Auditable trait
class Pembayaran extends Model
{
    use Auditable;
    
    // All changes automatically logged
}

// Manual audit logging
auditLog('payment_status_changed', [
    'old_status' => 'belum_bayar',
    'new_status' => 'lunas',
    'payment_id' => $pembayaran->id
]);
```

#### Audit Trail Export
```bash
# Export audit trails to CSV
GET /audit-trails/export?start_date=2025-01-01&end_date=2025-12-31

# View audit trail details
GET /audit-trails/{id}
```

## 💾 Backup & Restore

### 🔄 Database Backup System

#### Backup Features
- **Single effective button** - One-click backup
- **phpMyAdmin compatible** - Direct import support
- **Shared hosting friendly** - Works on limited hosting
- **Security checks** - Rate limiting and validation
- **Error handling** - Robust error reporting

#### Backup Process
```php
// Automatic backup with mysqldump
$command = "mysqldump --single-transaction --routines --triggers " .
           "--events --add-drop-table --complete-insert " .
           "--hex-blob --default-character-set=utf8mb4 " .
           "{$database} > {$backupFile}";
```

#### Restore Process
```php
// Multiple restore methods for compatibility
1. Standard MySQL Command
2. Laravel DB Import
3. Pure PHP SQL Parser
4. Chunked Import (for large files)
```

### 🛡️ Backup Security
- **Rate limiting** - Prevent abuse (1 hour cooldown)
- **File validation** - Only SQL files accepted
- **Size limits** - Maximum 50MB backup files
- **Path validation** - Prevent directory traversal
- **User authentication** - Only authorized users

## 🎨 UI/UX Features

### 📱 Mobile Responsiveness

#### Mobile Optimizations
- **Shortened payment codes** - Prevent overflow
- **PPPoE display** - Added to mobile payment view
- **Responsive tables** - Convert to cards on mobile
- **Touch-friendly buttons** - Proper sizing and spacing
- **Collapsible navigation** - Space-efficient menu

#### Responsive Design Examples
```css
/* Mobile-first responsive design */
.payment-card {
    @apply bg-white rounded-lg shadow p-4 mb-4;
}

@media (min-width: 768px) {
    .payment-card {
        @apply hidden; /* Hide on desktop */
    }
}

.payment-table {
    @apply hidden; /* Hide on mobile */
}

@media (min-width: 768px) {
    .payment-table {
        @apply table-auto w-full; /* Show on desktop */
    }
}
```

### 🎨 Professional Design

#### Invoice System
- **Clean header design** - No colored backgrounds
- **Professional layout** - Consistent spacing and typography
- **PDF optimization** - Proper margins and page breaks
- **Print-friendly** - Optimized for both screen and print

#### User Interface
- **Consistent styling** - Tailwind CSS framework
- **Intuitive navigation** - Clear menu structure
- **Loading states** - Smooth user experience
- **Error handling** - User-friendly error messages

## 🚀 New System Features

### 🎫 Customer Support System

#### Ticket Management
- **Customer Ticket Creation** - Customers can create support tickets
- **Admin Ticket Handling** - Complete admin interface for ticket management
- **Comment System** - Internal and external comments
- **File Attachments** - Support for file uploads
- **Status Tracking** - Open, In Progress, Resolved, Closed
- **Priority Levels** - Low, Medium, High, Urgent
- **Rating System** - Customer feedback on resolution

#### Admin Interface
```bash
# Access admin ticket management
/admin/tickets              # List all tickets
/admin/tickets/{id}         # Ticket detail with actions
/admin/tickets/statistics   # Ticket statistics
```

### 💳 Payment Proof System

#### Customer Features
- **Upload Payment Proof** - Secure file upload system
- **WhatsApp Integration** - Send proofs via WhatsApp
- **Status Tracking** - Pending, Verified, Rejected
- **File Preview** - Image and PDF preview support

#### Admin Features
- **Proof Verification** - Admin verification workflow
- **File Download** - Secure file download system
- **Admin Notes** - Verification notes and feedback
- **Bulk Processing** - Handle multiple proofs efficiently

#### Admin Interface
```bash
# Access admin payment proof management
/admin/payment-proofs              # List all payment proofs
/admin/payment-proofs/{id}         # Proof detail with verification
/admin/payment-proofs/statistics   # Proof statistics
```

### 🔐 Customer Authentication System

#### Features
- **Customer Login** - Secure authentication for customers
- **Password Management** - Change password functionality
- **Default Password** - Admin-generated default passwords
- **Session Management** - Secure session handling
- **Last Login Tracking** - Monitor customer activity

#### API Endpoints
```bash
# Customer authentication
POST /api/v1/customer/auth/login           # Customer login
POST /api/v1/customer/auth/logout          # Customer logout
GET  /api/v1/customer/auth/me              # Get customer profile
POST /api/v1/customer/auth/change-password # Change password
```

### 📱 Customer Self-Service Portal

#### Mobile/Web App Support
- **Complete RESTful API** - Full customer self-service
- **Payment Management** - View bills, upload proofs, check status
- **Profile Management** - Update customer information
- **Support System** - Create and manage tickets
- **Statistics Dashboard** - Customer statistics and history

#### API Structure
```bash
# Customer API endpoints
/api/v1/customer/auth/*      # Authentication
/api/v1/customer/payment/*   # Payment management
/api/v1/customer/support/*   # Ticket system
/api/v1/customer/profile/*   # Profile management
```

### 🛡️ Enhanced Security

#### New Permissions
- **Ticket Permissions** - view-ticket, edit-ticket, assign-ticket, resolve-ticket
- **Payment Proof Permissions** - view-payment-proof, verify-payment-proof, download-payment-proof
- **API Permissions** - Customer API access control
- **File Upload Security** - Secure file handling with validation

#### Security Features
- **Laravel Sanctum** - API token authentication
- **File Upload Validation** - Secure file type and size validation
- **Rate Limiting** - API rate limiting for security
- **Permission-based Access** - Granular access control

### 📊 System Integration

#### Unified Workflow
1. **Customer** creates ticket via mobile app
2. **Admin** handles ticket via web interface
3. **Customer** uploads payment proof via mobile app
4. **Admin** verifies payment proof via web interface
5. **System** automatically updates payment status

#### Benefits
- **Seamless Integration** - Admin and customer systems work together
- **Efficient Workflow** - Streamlined processes
- **Real-time Updates** - Instant status updates
- **Complete Audit Trail** - All actions tracked

### 🖥️ MikroTik Router Management

#### Features
- **Router Management** - Add, edit, delete MikroTik routers
- **Connection Testing** - Test API connection to routers
- **PPPoE Search** - Search PPPoE users in routers
- **Status Monitoring** - Real-time router status tracking
- **Dashboard** - Router resource usage and statistics
- **Auto-Sync** - Automatic PPPoE status checking for customers

#### Router Configuration
```bash
# Access MikroTik management
/mikrotiks              # List all routers
/mikrotiks/create        # Add new router
/mikrotiks/{id}          # Router dashboard
/mikrotiks/{id}/edit     # Edit router
```

#### PPPoE Integration
- **Auto-Check** - Automatically check PPPoE status when viewing customer details
- **Status Display** - Show "Ada di MikroTik" or "Tidak ada di MikroTik" badge
- **Router Info** - Display router name, IP, profile, and status
- **Multi-Router Support** - Check across multiple routers

#### RouterOS API Setup
1. Enable API service on RouterOS:
   ```bash
   /ip service
   set api disabled=no port=8728
   ```
2. Create API user (or use admin):
   ```bash
   /user add name=api-user password=your-password group=full
   ```
3. Add router in system with IP, port, username, and password

### 🗺️ ODP & Mapping System

#### Features
- **ODP Management** - Create and manage Optical Distribution Points
- **Interactive Mapping** - Leaflet.js map with satellite view
- **Customer Mapping** - Visualize customer locations on map
- **ODP-Customer Connection** - Link customers to ODPs
- **Port Tracking** - Automatic port usage calculation
- **Location Picker** - Click-to-select coordinates on map

#### Mapping Features
- **Map View** - Interactive map showing all ODPs and customers
- **Layer Toggle** - Switch between standard and satellite view
- **Filter System** - Filter by status, ODP, or search
- **Customer Details** - Click customer marker for details
- **ODP Details** - View ODP capacity and connected customers

#### ODP Management
```bash
# Access ODP management
/odps              # List all ODPs
/odps/create       # Add new ODP
/odps/{id}         # ODP details with map
/odps/{id}/edit    # Edit ODP
/mapping           # Main mapping page
```

## 🚨 Troubleshooting

### 🔧 Common Issues & Solutions

#### 1. Payment Edit Not Working
**Problem**: Payment edit form not saving
**Solution**: Check immutable principle implementation
```php
// Ensure only allowed fields are updated
$updateData = $request->only(['status', 'tanggal_bayar', 'keterangan']);
$pembayaran->update($updateData);
```

#### 2. PDF Layout Issues
**Problem**: PDF layout different from browser print
**Solution**: Use consistent CSS for both
```css
@media print {
    .action-buttons { display: none !important; }
    body { margin: 0; padding: 10px; }
    .invoice-container { max-width: 100%; margin: 0; }
}
```

#### 3. Mobile Layout Problems
**Problem**: Elements overflowing on mobile
**Solution**: Use responsive design classes
```html
<div class="truncate max-w-xs sm:max-w-none">
    {{ $longText }}
</div>
```

#### 4. Database Restore Failing
**Problem**: Restore not working on shared hosting
**Solution**: Use pure PHP method
```php
// Method 2: Laravel DB Import (most compatible)
$statements = explode(';', $sqlContent);
foreach ($statements as $statement) {
    if (trim($statement)) {
        DB::statement($statement);
    }
}
```

#### 5. Audit Trail Empty
**Problem**: No audit trail entries
**Solution**: Ensure Auditable trait is used
```php
class Pembayaran extends Model
{
    use Auditable; // ← This is required
}
```

#### 6. Customer API Authentication Issues
**Problem**: Customer login not working
**Solution**: Check customer authentication setup
```php
// Ensure Pelanggan model extends Authenticatable
class Pelanggan extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    // Ensure password field is fillable
    protected $fillable = ['password', 'remember_token', ...];
}
```

#### 7. File Upload Not Working
**Problem**: Payment proof upload fails
**Solution**: Check file storage configuration
```php
// Ensure storage link is created
php artisan storage:link

// Check file permissions
chmod -R 755 storage/app/public
```

#### 8. Ticket System Not Loading
**Problem**: Admin ticket pages show errors
**Solution**: Check permissions and migrations
```bash
# Run ticket migrations
php artisan migrate

# Check permissions
php artisan db:seed --class=TicketPaymentProofPermissionSeeder

# Verify routes
php artisan route:list --name=admin.tickets
```

#### 9. API Rate Limiting Issues
**Problem**: API requests being blocked
**Solution**: Check rate limiting configuration
```php
// In routes/api.php, ensure proper rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    // API routes
});
```

#### 10. MikroTik Connection Failed
**Problem**: Cannot connect to MikroTik router
**Solution**: Check router configuration
```bash
# 1. Verify API service is enabled
/ip service print where name=api

# 2. Check firewall rules
/ip firewall filter print

# 3. Verify user permissions
/user print where name=api-user

# 4. Test connection from server
telnet router-ip 8728
```

#### 11. PPPoE Not Found in Router
**Problem**: PPPoE exists in database but not found in router
**Solution**: 
- Check username spelling (case-sensitive)
- Verify router is online and accessible
- Check if PPPoE is disabled in router
- Try manual search in router dashboard

#### 12. ODP Port Count Incorrect
**Problem**: Port terpakai tidak sesuai dengan jumlah pelanggan aktif
**Solution**: 
- System automatically updates on customer create/update/delete
- Manually sync: Visit ODP list page (auto-sync on load)
- Check customer status (only 'aktif' customers count)

### 🔍 Debug Commands
```bash
# Check route permissions
php artisan route:list --name=audit-trails

# Test data integrity
php artisan tinker
>>> App\Models\Pembayaran::count()

# Check audit trail
php artisan tinker
>>> App\Models\AuditTrail::count()

# Test backup functionality
php artisan backup:database

# Check MikroTik routes
php artisan route:list --name=mikrotiks

# Test MikroTik connection (via controller)
# Visit /mikrotiks/{id} and click "Test Connection"

# Sync ODP port usage
# Visit /odps - auto-syncs on page load
```

## 📚 Documentation

### 📖 Additional Resources

#### Complete Documentation
- **Database Schema**: `docs/DATABASE_SCHEMA.md` - Complete database structure with all tables
- **API Documentation**: `docs/API_DOCUMENTATION.md` - Full API reference for customer portal
- **MikroTik Guide**: `docs/MIKROTIK_GUIDE.md` - Complete MikroTik integration guide
- **Deployment Guide**: `docs/DEPLOYMENT_GUIDE.md` - Production deployment instructions
- **System Overview**: `docs/SYSTEM_OVERVIEW.md` - System architecture and design
- **Complete Documentation**: `docs/COMPLETE_DOCUMENTATION.md` - Comprehensive system documentation

#### Quick Reference
```bash
# Essential commands
php artisan route:list                    # View all routes
php artisan migrate:status               # Check migration status
php artisan bills:generate-smart         # Generate smart bills
php artisan backup:database              # Create backup
php artisan audit:clean --days=90        # Clean old audit trails

# Development commands
php artisan make:controller ApiController # Create API controller
php artisan make:model ModelName -m      # Create model with migration
php artisan make:seeder SeederName       # Create seeder
```

### 🎯 Best Practices

#### Development
1. **Always use immutable principle** for payment data
2. **Implement proper validation** for all inputs
3. **Use eager loading** to prevent N+1 queries
4. **Handle null values** gracefully in views
5. **Test on mobile devices** regularly
6. **Use non-breaking changes** for new features (like MikroTik integration)
7. **Implement graceful error handling** for external services

#### Deployment
1. **Set proper file permissions** (755 for directories, 644 for files)
2. **Configure cron jobs** for scheduled tasks
3. **Enable SSL/HTTPS** for production
4. **Set up regular backups** (daily recommended)
5. **Monitor error logs** regularly
6. **Test MikroTik connections** before going live
7. **Verify ODP and mapping data** accuracy

#### Security
1. **Never store sensitive data** in plain text
2. **Use HTTPS** for all communications
3. **Implement rate limiting** for sensitive operations
4. **Regular security updates** for dependencies
5. **Audit user permissions** regularly
6. **Encrypt MikroTik passwords** (already implemented)
7. **Limit API access** to trusted IPs if possible

#### MikroTik Integration
1. **Use dedicated API user** instead of admin account
2. **Test connections regularly** to ensure routers are accessible
3. **Monitor router status** in dashboard
4. **Handle connection errors gracefully** (non-breaking)
5. **Cache connection results** to reduce API calls
6. **Document router configurations** for troubleshooting

---

## 🎉 System Status

### ✅ Production Ready Features
- **✅ Data Integrity**: Immutable payment records
- **✅ Security**: Role-based access control
- **✅ Performance**: Optimized queries and caching
- **✅ Mobile**: Responsive design
- **✅ Audit**: Complete change tracking
- **✅ Backup**: Reliable backup/restore
- **✅ API**: Payment gateway ready
- **✅ PWA**: Offline functionality
- **✅ MikroTik Integration**: Router management and monitoring
- **✅ Mapping System**: ODP and customer location tracking
- **✅ Customer Portal**: Complete self-service API

### 🚀 Ready for Production
**This system is production-ready with enterprise-level features including immutable data integrity, comprehensive audit trails, mobile-responsive design, MikroTik router integration, location mapping, and robust security measures.**

---

## 📞 Support & Contact

- **Developer**: AI Assistant
- **Version**: 4.0.0
- **Last Updated**: November 2024
- **License**: MIT
- **Repository**: [GitHub Repository](https://github.com/yourusername/wifi-billing-system)

---

## 📚 Complete Documentation

### 📖 Main Documentation Files

- **[README.md](README.md)** - This file, overview and quick start
- **[Database Schema](docs/DATABASE_SCHEMA.md)** - Complete database structure
- **[API Documentation](docs/API_DOCUMENTATION.md)** - Complete API reference
- **[MikroTik Guide](docs/MIKROTIK_GUIDE.md)** - MikroTik integration guide
- **[Deployment Guide](docs/DEPLOYMENT_GUIDE.md)** - Production deployment instructions
- **[System Overview](docs/SYSTEM_OVERVIEW.md)** - System architecture overview
- **[Complete Documentation](docs/COMPLETE_DOCUMENTATION.md)** - Comprehensive system documentation

### 🎯 Quick Links

- **Installation**: [Quick Start](#-quick-start)
- **Database**: [Database Schema](#-database-schema)
- **API**: [API Documentation](#️-api-documentation)
- **MikroTik**: [MikroTik Guide](docs/MIKROTIK_GUIDE.md)
- **Troubleshooting**: [Troubleshooting](#-troubleshooting)

---

**🌟 Star this repository if you find it helpful!**
