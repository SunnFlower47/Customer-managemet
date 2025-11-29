# 🏗️ Rancangan Sistem MikroTik Integration

## 📋 **Overview**
Dokumentasi rancangan sistem integrasi MikroTik untuk WiFi Management System yang akan dikomersialkan untuk ISP lain. Sistem ini mendukung multiple MikroTik router dengan port forwarding dan auto disable untuk pelanggan yang telat bayar.

---

## 🎯 **Requirements dari Diskusi**

### **1. Hardware & Network Setup**
- **4 MikroTik Router** dengan RouterOS v6+ (bukan v7)
- **1 IP Public** untuk semua router dengan port forwarding
- **Port Forwarding**: 
  - Port 8728 → MikroTik 1 (Desa Wantilan)
  - Port 8729 → MikroTik 2 (Desa Wantilan - backup)
  - Port 8730 → MikroTik 3 (Desa Cijoged)
  - Port 8731 → MikroTik 4 (Desa Cijoged - backup)
- **Area Coverage**: Desa Wantilan dan Desa Cijoged, Subang

### **2. Data yang Perlu Disiapkan**
- **IP Public**: IP public yang digunakan
- **API Credentials**: Username dan password untuk API
- **Customer Data**: Data pelanggan di kedua desa
- **PPPoE Users**: Data PPPoE yang sudah ada di MikroTik

### **3. Fitur yang Dibutuhkan**
- **Real-time Sync**: Sync data PPPoE users dari MikroTik
- **Auto Disable**: Otomatis disable user yang telat bayar
- **Multi-Router Support**: Support 4 router dengan port forwarding
- **Dynamic User Detection**: User bisa dipindah antar router
- **Dashboard Monitoring**: Monitor status semua router

---

## 🏗️ **Arsitektur Sistem**

### **1. Database Schema**

#### **Tabel mikrotik_routers**
```sql
CREATE TABLE mikrotik_routers (
    id BIGINT PRIMARY KEY,
    name VARCHAR(100), -- Router-1, Router-2, dll
    ip_public VARCHAR(45), -- IP public yang sama
    port INT, -- Port yang berbeda (8728, 8729, 8730, 8731)
    username VARCHAR(100), -- Username API
    password VARCHAR(255), -- Password API (encrypted)
    is_active BOOLEAN DEFAULT true,
    location VARCHAR(255), -- Desa Wantilan, Desa Cijoged
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### **Tabel mikrotik_pppoe_users**
```sql
CREATE TABLE mikrotik_pppoe_users (
    id BIGINT PRIMARY KEY,
    pelanggan_id BIGINT, -- FK ke pelanggans
    mikrotik_router_id BIGINT, -- FK ke mikrotik_routers
    mikrotik_username VARCHAR(255), -- Username PPPoE
    mikrotik_password VARCHAR(255), -- Password PPPoE
    profile_name VARCHAR(100), -- Profile bandwidth
    local_address VARCHAR(45), -- IP local
    remote_address VARCHAR(45), -- IP remote
    last_sync_at TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_username (mikrotik_username),
    INDEX idx_router (mikrotik_router_id),
    INDEX idx_pelanggan (pelanggan_id)
);
```

#### **Tabel mikrotik_logs**
```sql
CREATE TABLE mikrotik_logs (
    id BIGINT PRIMARY KEY,
    mikrotik_router_id BIGINT, -- FK ke mikrotik_routers
    pelanggan_id BIGINT, -- FK ke pelanggans
    action VARCHAR(50), -- sync, disable, enable, error
    status VARCHAR(20), -- success, failed
    response TEXT,
    created_at TIMESTAMP
);
```

### **2. Multi-Tenant Support (Untuk Komersialisasi)**

#### **Tabel isps**
```sql
CREATE TABLE isps (
    id BIGINT PRIMARY KEY,
    name VARCHAR(100), -- Nama ISP
    domain VARCHAR(100), -- Domain ISP
    contact_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🔧 **Komponen Sistem**

### **1. Models**

#### **MikroTikRouter Model**
```php
class MikroTikRouter extends Model
{
    protected $fillable = [
        'name', 'ip_public', 'port', 'username', 'password',
        'is_active', 'location', 'description'
    ];
    
    protected $casts = [
        'password' => 'encrypted',
        'is_active' => 'boolean'
    ];
    
    public function pppoeUsers()
    {
        return $this->hasMany(MikroTikPPPoEUser::class);
    }
    
    public function logs()
    {
        return $this->hasMany(MikroTikLog::class);
    }
    
    public function testConnection()
    {
        // Method untuk test koneksi ke router
    }
}
```

#### **MikroTikPPPoEUser Model**
```php
class MikroTikPPPoEUser extends Model
{
    protected $fillable = [
        'pelanggan_id', 'mikrotik_router_id', 'mikrotik_username',
        'mikrotik_password', 'profile_name', 'local_address',
        'remote_address', 'is_active', 'last_sync_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime'
    ];
    
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    
    public function router()
    {
        return $this->belongsTo(MikroTikRouter::class);
    }
}
```

### **2. Services**

#### **MikroTikService**
```php
class MikroTikService
{
    protected $routers;
    
    public function __construct()
    {
        $this->routers = MikroTikRouter::where('is_active', true)->get();
    }
    
    // Connect ke router dengan port forwarding
    private function connectToRouter($router)
    
    // Sync PPPoE users dari semua router
    public function syncAllPPPoEUsers()
    
    // Sync PPPoE users dari router tertentu
    public function syncPPPoEUsersFromRouter($router)
    
    // Find user di router manapun
    public function findUserInAnyRouter($username)
    
    // Disable user di router manapun
    public function disablePPPoEUser($pelangganId)
    
    // Enable user di router manapun
    public function enablePPPoEUser($pelangganId)
    
    // Get user status dari semua router
    public function getUserStatus($username)
    
    // Test koneksi ke router
    public function testRouterConnection($routerId)
}
```

### **3. Controllers**

#### **MikroTikDashboardController**
```php
class MikroTikDashboardController extends Controller
{
    public function index() // Dashboard utama
    public function syncUsers() // Sync semua users
    public function disableUser($pelangganId) // Disable user
    public function enableUser($pelangganId) // Enable user
    public function getUserStatus($username) // Get status user
    public function testConnection($routerId) // Test koneksi
    public function getRouterDetails($routerId) // Detail router
}
```

#### **MikroTikRouterController**
```php
class MikroTikRouterController extends Controller
{
    public function index() // List router
    public function create() // Form tambah router
    public function store(Request $request) // Simpan router
    public function show($id) // Detail router
    public function edit($id) // Form edit router
    public function update(Request $request, $id) // Update router
    public function destroy($id) // Hapus router
    public function testConnection($id) // Test koneksi
}
```

### **4. Commands**

#### **SyncPPPoEUsers Command**
```php
class SyncPPPoEUsers extends Command
{
    protected $signature = 'mikrotik:sync-pppoe-users';
    protected $description = 'Sync PPPoE users from all MikroTik routers';
    
    public function handle()
    {
        // Sync users dari semua router
    }
}
```

#### **AutoDisableOverdueUsers Command**
```php
class AutoDisableOverdueUsers extends Command
{
    protected $signature = 'mikrotik:auto-disable-overdue';
    protected $description = 'Auto disable PPPoE users who are overdue on payment';
    
    public function handle()
    {
        // Cari pelanggan telat bayar dan disable
    }
}
```

#### **CheckRouterHealth Command**
```php
class CheckRouterHealth extends Command
{
    protected $signature = 'mikrotik:check-health';
    protected $description = 'Check health of all MikroTik routers';
    
    public function handle()
    {
        // Cek kesehatan semua router
    }
}
```

### **5. Views**

#### **Dashboard Views**
```
resources/views/mikrotik/
├── dashboard.blade.php          # Dashboard utama
├── routers/
│   ├── index.blade.php          # List router
│   ├── create.blade.php         # Form tambah router
│   ├── edit.blade.php           # Form edit router
│   └── show.blade.php           # Detail router
├── users/
│   ├── index.blade.php          # List PPPoE users
│   └── show.blade.php           # Detail user
└── logs/
    └── index.blade.php          # Log aktivitas
```

### **6. Routes**

#### **Web Routes**
```php
Route::prefix('mikrotik')->name('mikrotik.')->group(function () {
    Route::get('/', [MikroTikDashboardController::class, 'index'])->name('dashboard');
    Route::post('/sync', [MikroTikDashboardController::class, 'syncUsers'])->name('sync');
    Route::post('/disable/{pelanggan}', [MikroTikDashboardController::class, 'disableUser'])->name('disable');
    Route::post('/enable/{pelanggan}', [MikroTikDashboardController::class, 'enableUser'])->name('enable');
    Route::get('/user-status/{username}', [MikroTikDashboardController::class, 'getUserStatus'])->name('user.status');
    Route::get('/test-connection/{router}', [MikroTikDashboardController::class, 'testConnection'])->name('test.connection');
    
    Route::resource('routers', MikroTikRouterController::class);
    Route::get('users', [MikroTikUserController::class, 'index'])->name('users.index');
    Route::get('logs', [MikroTikLogController::class, 'index'])->name('logs.index');
});
```

#### **API Routes**
```php
Route::prefix('api/mikrotik')->name('api.mikrotik.')->group(function () {
    Route::get('/status', [MikroTikApiController::class, 'getStatus']);
    Route::post('/sync', [MikroTikApiController::class, 'syncUsers']);
    Route::post('/disable/{pelanggan}', [MikroTikApiController::class, 'disableUser']);
    Route::post('/enable/{pelanggan}', [MikroTikApiController::class, 'enableUser']);
    Route::get('/user-status/{username}', [MikroTikApiController::class, 'getUserStatus']);
});
```

---

## 🚀 **Workflow Sistem**

### **1. Setup Process**
1. **Install Dependencies**: Install MikroTik API library
2. **Create Database**: Run migrations untuk create tables
3. **Setup Routers**: Input data 4 router dengan port forwarding
4. **Test Connections**: Test koneksi ke semua router
5. **Initial Sync**: Sync data PPPoE users pertama kali

### **2. Daily Operations**
1. **Auto Sync**: Sync PPPoE users setiap 5 menit
2. **Health Check**: Cek kesehatan router setiap 2 menit
3. **Auto Disable**: Disable user telat bayar setiap jam
4. **Monitoring**: Monitor status real-time di dashboard

### **3. User Management**
1. **Add User**: Tambah user baru di MikroTik
2. **Sync User**: Sync user ke database
3. **Map Customer**: Map user dengan data pelanggan
4. **Monitor Status**: Monitor status koneksi user

---

## 📊 **Dashboard Features**

### **1. Main Dashboard**
- **Router Status**: Status semua 4 router
- **User Statistics**: Total users per router
- **Location Summary**: Summary per desa
- **Real-time Updates**: Update status real-time

### **2. Router Management**
- **Add Router**: Tambah router baru
- **Edit Router**: Edit konfigurasi router
- **Test Connection**: Test koneksi ke router
- **View Details**: Lihat detail router

### **3. User Management**
- **List Users**: List semua PPPoE users
- **User Status**: Status koneksi user
- **Disable/Enable**: Manual disable/enable user
- **User History**: Riwayat aktivitas user

### **4. Monitoring**
- **Connection Logs**: Log koneksi ke router
- **Activity Logs**: Log aktivitas sistem
- **Error Logs**: Log error dan troubleshooting
- **Performance Metrics**: Metrik performa sistem

---

## 🔐 **Security Features**

### **1. API Security**
- **Encrypted Passwords**: Password MikroTik di-encrypt
- **IP Restriction**: Restrict akses API ke IP tertentu
- **Rate Limiting**: Limit API calls per minute
- **Access Control**: Control akses berdasarkan role

### **2. Data Security**
- **Database Encryption**: Encrypt sensitive data
- **Audit Trail**: Log semua aktivitas
- **Backup Strategy**: Regular backup database
- **Access Logging**: Log akses ke sistem

---

## 🧪 **Testing Strategy**

### **1. Unit Tests**
- **Service Tests**: Test MikroTik service
- **Model Tests**: Test database models
- **Controller Tests**: Test API endpoints
- **Command Tests**: Test Laravel commands

### **2. Integration Tests**
- **API Integration**: Test MikroTik API calls
- **Database Integration**: Test database operations
- **Queue Integration**: Test background jobs
- **Scheduled Tasks**: Test cron jobs

### **3. Manual Tests**
- **Connection Tests**: Test koneksi ke router
- **Sync Tests**: Test sync data
- **Disable Tests**: Test auto disable
- **Dashboard Tests**: Test dashboard functionality

---

## 📈 **Performance Considerations**

### **1. Optimization**
- **Connection Pooling**: Pool koneksi ke MikroTik
- **Caching**: Cache data yang sering diakses
- **Queue Jobs**: Background jobs untuk heavy operations
- **Database Indexing**: Index untuk query yang sering

### **2. Scalability**
- **Multi-Tenant**: Support multiple ISP
- **Load Balancing**: Balance load antar router
- **Failover**: Auto failover jika router down
- **Horizontal Scaling**: Scale sistem secara horizontal

---

## 🚀 **Deployment Checklist**

### **1. Pre-Deployment**
- [ ] Install MikroTik API library
- [ ] Create database tables
- [ ] Setup router data
- [ ] Test connections
- [ ] Configure scheduled tasks

### **2. Deployment**
- [ ] Deploy code to server
- [ ] Run database migrations
- [ ] Setup environment variables
- [ ] Configure cron jobs
- [ ] Test all functionality

### **3. Post-Deployment**
- [ ] Monitor system performance
- [ ] Check error logs
- [ ] Verify scheduled tasks
- [ ] Test user operations
- [ ] Document any issues

---

## 📋 **Data yang Perlu Disiapkan**

### **1. Router Data**
```php
$routers = [
    [
        'name' => 'Router-1',
        'ip_public' => 'YOUR_IP_PUBLIC',
        'port' => 8728,
        'username' => 'api-user',
        'password' => 'your_password',
        'location' => 'Desa Wantilan',
        'description' => 'Router untuk area Desa Wantilan, Subang'
    ],
    [
        'name' => 'Router-2',
        'ip_public' => 'YOUR_IP_PUBLIC',
        'port' => 8729,
        'username' => 'api-user',
        'password' => 'your_password',
        'location' => 'Desa Wantilan',
        'description' => 'Router untuk area Desa Wantilan, Subang (backup)'
    ],
    [
        'name' => 'Router-3',
        'ip_public' => 'YOUR_IP_PUBLIC',
        'port' => 8730,
        'username' => 'api-user',
        'password' => 'your_password',
        'location' => 'Desa Cijoged',
        'description' => 'Router untuk area Desa Cijoged, Subang'
    ],
    [
        'name' => 'Router-4',
        'ip_public' => 'YOUR_IP_PUBLIC',
        'port' => 8731,
        'username' => 'api-user',
        'password' => 'your_password',
        'location' => 'Desa Cijoged',
        'description' => 'Router untuk area Desa Cijoged, Subang (backup)'
    ]
];
```

### **2. Customer Data**
- **Total Pelanggan**: Berapa total pelanggan di database
- **PPPoE Usernames**: List username PPPoE yang ada
- **Payment Status**: Status pembayaran untuk auto disable
- **Package Data**: Data paket yang digunakan

---

## 🎯 **Next Steps**

### **1. Implementation Priority**
1. **High Priority**: Database setup, basic service, connection test
2. **Medium Priority**: Dashboard, sync functionality, auto disable
3. **Low Priority**: Advanced features, monitoring, optimization

### **2. Development Phases**
1. **Phase 1**: Basic setup dan connection test
2. **Phase 2**: Sync functionality dan dashboard
3. **Phase 3**: Auto disable dan monitoring
4. **Phase 4**: Advanced features dan optimization

---

**📅 Created**: $(date)  
**👤 By**: AI Assistant  
**🎯 Purpose**: Rancangan Sistem MikroTik Integration untuk WiFi Management System
