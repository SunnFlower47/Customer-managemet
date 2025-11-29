# 📊 Database Schema - WiFi Customer Management System

**Last Updated**: November 2024  
**Version**: 4.0.0

## Complete Database Structure

> **Note**: This schema includes all tables including the latest features: MikroTik integration, ODP management, and location mapping.

### 1. Users Table
**Purpose**: Store system users (admin, penagih)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | User ID |
| name | varchar(255) | NOT NULL | User's full name |
| email | varchar(255) | UNIQUE, NOT NULL | User's email address |
| email_verified_at | timestamp | NULLABLE | Email verification timestamp |
| password | varchar(255) | NOT NULL | Hashed password |
| role | varchar(255) | NOT NULL | User role (admin, penagih, etc.) |
| aktif | boolean | DEFAULT true | User status |
| remember_token | varchar(100) | NULLABLE | Remember me token |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (email)

### 2. Pakets Table
**Purpose**: Store internet packages

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Package ID |
| nama_paket | varchar(255) | NOT NULL | Package name |
| harga | decimal(10,2) | NOT NULL | Package price |
| kecepatan | varchar(255) | NULLABLE | Internet speed |
| deskripsi | text | NULLABLE | Package description |
| aktif | boolean | DEFAULT true | Package status |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (aktif)

**Relationships**:
- hasMany: Pelanggan
- hasMany: Pembayaran

### 3. Penagihs Table
**Purpose**: Store bill collectors

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Collector ID |
| nama | varchar(255) | NOT NULL | Collector name |
| email | varchar(255) | NULLABLE | Collector email |
| no_hp | varchar(255) | NULLABLE | Collector phone |
| alamat | text | NULLABLE | Collector address |
| user_id | bigint | NULLABLE, FOREIGN KEY | Associated user ID |
| aktif | boolean | DEFAULT true | Collector status |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (aktif, user_id)
- FOREIGN KEY (user_id) REFERENCES users(id)

**Relationships**:
- belongsTo: User
- hasMany: Pelanggan
- hasMany: Pembayaran

### 4. Pelanggans Table
**Purpose**: Store customers

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Customer ID |
| nama | varchar(255) | NOT NULL | Customer name |
| pppoe | varchar(255) | UNIQUE, NOT NULL | PPPoE username |
| alamat | text | NOT NULL | Customer address |
| no_hp | varchar(255) | NOT NULL | Customer phone |
| paket_id | bigint | NOT NULL, FOREIGN KEY | Package ID |
| tanggal_mulai | date | NOT NULL | Service start date |
| tanggal_pembayaran | integer | DEFAULT 1 | Payment day of month (1-31) |
| penagih_id | bigint | NULLABLE, FOREIGN KEY | Collector ID |
| status | enum('aktif', 'isolir', 'bayar double') | DEFAULT 'aktif' | Customer status |
| latitude | decimal(10,8) | NULLABLE | Customer latitude |
| longitude | decimal(11,8) | NULLABLE | Customer longitude |
| odp_id | bigint | NULLABLE, FOREIGN KEY | ODP ID |
| mikrotik_id | bigint | NULLABLE, FOREIGN KEY | MikroTik router ID |
| exists_in_mikrotik | boolean | NULLABLE | PPPoE exists in router |
| mikrotik_last_checked | timestamp | NULLABLE | Last MikroTik check time |
| mikrotik_router_name | varchar(255) | NULLABLE | Router name where PPPoE found |
| mikrotik_status | varchar(255) | NULLABLE | PPPoE status in router |
| mikrotik_ip | varchar(255) | NULLABLE | IP address from router |
| mikrotik_profile | varchar(255) | NULLABLE | PPPoE profile in router |
| password | varchar(255) | NULLABLE | Customer login password (hashed) |
| remember_token | varchar(100) | NULLABLE | Remember me token |
| last_login_at | timestamp | NULLABLE | Last login timestamp |
| is_default_password | boolean | DEFAULT true | Default password flag |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (pppoe)
- INDEX (status, penagih_id)
- INDEX (paket_id, status)
- INDEX (tanggal_pembayaran)
- FOREIGN KEY (paket_id) REFERENCES pakets(id)
- FOREIGN KEY (penagih_id) REFERENCES penagihs(id)

**Relationships**:
- belongsTo: Paket
- belongsTo: Penagih
- belongsTo: Odp (nullable)
- belongsTo: Mikrotik (nullable)
- hasMany: Pembayaran
- hasMany: CustomerPackage (package history)

### 5. Pembayarans Table (IMMUTABLE)
**Purpose**: Store payment records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Payment ID |
| kode_pembayaran | varchar(255) | UNIQUE, NOT NULL | Payment code |
| pelanggan_id | bigint | NOT NULL, FOREIGN KEY | Customer ID |
| paket_id | bigint | NULLABLE, FOREIGN KEY | Package ID (historical) |
| nama_paket | varchar(255) | NULLABLE | Package name (historical) |
| harga_paket | decimal(10,2) | NULLABLE | Package price (historical) |
| bulan_tagihan | integer | NOT NULL | Billing month (1-12) |
| tahun_tagihan | integer | NOT NULL | Billing year |
| jumlah | decimal(10,2) | NOT NULL | Payment amount |
| status | enum('belum_bayar', 'lunas') | DEFAULT 'belum_bayar' | Payment status |
| tanggal_bayar | timestamp | NULLABLE | Payment date |
| penagih_id | bigint | NULLABLE, FOREIGN KEY | Collector ID |
| nama_penagih | varchar(255) | NULLABLE | Collector name (historical) |
| keterangan | text | NULLABLE | Payment notes |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (kode_pembayaran)
- UNIQUE KEY (pelanggan_id, bulan_tagihan, tahun_tagihan)
- INDEX (status, penagih_id)
- INDEX (bulan_tagihan, tahun_tagihan)
- INDEX (pelanggan_id, bulan_tagihan, tahun_tagihan)
- INDEX (tanggal_bayar)
- FOREIGN KEY (pelanggan_id) REFERENCES pelanggans(id)
- FOREIGN KEY (paket_id) REFERENCES pakets(id) ON DELETE SET NULL
- FOREIGN KEY (penagih_id) REFERENCES penagihs(id) ON DELETE SET NULL

**Relationships**:
- belongsTo: Pelanggan
- belongsTo: Paket (nullable)
- belongsTo: Penagih (nullable)

### 6. Pengeluarans Table
**Purpose**: Store expense records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Expense ID |
| kategori | varchar(255) | NOT NULL | Expense category |
| nama_pengeluaran | varchar(255) | NOT NULL | Expense name |
| deskripsi | text | NULLABLE | Expense description |
| jumlah | decimal(15,2) | NOT NULL | Expense amount |
| tanggal_pengeluaran | date | NOT NULL | Expense date |
| metode_pembayaran | varchar(255) | DEFAULT 'tunai' | Payment method |
| status | varchar(255) | DEFAULT 'terkonfirmasi' | Expense status |
| bukti_pembayaran | varchar(255) | NULLABLE | Proof of payment file path |
| user_id | bigint | NOT NULL, FOREIGN KEY | User who created the expense |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (tanggal_pengeluaran, kategori)
- INDEX (status, tanggal_pengeluaran)
- INDEX (user_id)
- FOREIGN KEY (user_id) REFERENCES users(id)

**Relationships**:
- belongsTo: User

### 7. Customer Packages Table
**Purpose**: Store customer package history

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | History ID |
| customer_id | bigint | NOT NULL, FOREIGN KEY | Customer ID |
| package_id | bigint | NOT NULL, FOREIGN KEY | Package ID |
| start_date | date | NOT NULL | Package start date |
| end_date | date | NULLABLE | Package end date |
| is_active | boolean | DEFAULT true | Active status |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (customer_id, start_date)
- INDEX (package_id)
- INDEX (is_active)
- FOREIGN KEY (customer_id) REFERENCES pelanggans(id)
- FOREIGN KEY (package_id) REFERENCES pakets(id)

**Relationships**:
- belongsTo: Pelanggan
- belongsTo: Paket

### 8. Company Profiles Table
**Purpose**: Store company information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Profile ID |
| nama_perusahaan | varchar(255) | NOT NULL | Company name |
| nama_lengkap_perusahaan | varchar(255) | NULLABLE | Full company name |
| inisial_perusahaan | varchar(255) | NULLABLE | Company initials |
| alamat | text | NULLABLE | Company address |
| nomor_kontak | varchar(255) | NULLABLE | Company phone |
| whatsapp | varchar(255) | NULLABLE | WhatsApp number |
| email_support | varchar(255) | NULLABLE | Support email |
| website | varchar(255) | NULLABLE | Company website |
| logo_path | varchar(255) | NULLABLE | Logo file path |
| deskripsi | text | NULLABLE | Company description |
| payment_code_prefix | varchar(10) | DEFAULT 'PAY' | Payment code prefix |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)

### 9. Audit Trails Table
**Purpose**: Store audit logs

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Audit ID |
| user_type | varchar(255) | NULLABLE | User type |
| user_id | bigint | NULLABLE | User ID |
| event | varchar(255) | NOT NULL | Event type |
| auditable_type | varchar(255) | NOT NULL | Model type |
| auditable_id | bigint | NOT NULL | Model ID |
| old_values | json | NULLABLE | Old values |
| new_values | json | NULLABLE | New values |
| url | varchar(255) | NULLABLE | Request URL |
| ip_address | varchar(45) | NULLABLE | IP address |
| user_agent | varchar(255) | NULLABLE | User agent |
| tags | varchar(255) | NULLABLE | Tags |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (auditable_type, auditable_id)
- INDEX (user_type, user_id)

### 10. Backup Histories Table
**Purpose**: Store backup history

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Backup ID |
| filename | varchar(255) | NOT NULL | Backup filename |
| file_path | varchar(255) | NOT NULL | Backup file path |
| file_size | bigint | NOT NULL | File size in bytes |
| backup_type | varchar(255) | NOT NULL | Backup type |
| status | varchar(255) | NOT NULL | Backup status |
| user_id | bigint | NULLABLE, FOREIGN KEY | User who created backup |
| created_at | timestamp | NOT NULL | Record creation time |
| updated_at | timestamp | NOT NULL | Record update time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (backup_type, status)
- INDEX (user_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL

## Data Relationships

### Entity Relationship Diagram (ERD)
```
Users (1) -----> (0..1) Penagihs
  |                    |
  |                    |
  v                    v
Pengeluarans      Pelanggans (1) -----> (1) Pakets
  |                    |                    |
  |                    |                    |
  v                    v                    v
Backup Histories   Pembayarans (0..1) -----> (1) Pakets
                      |
                      |
                      v
                 Customer Packages
```

### Key Relationships
1. **User → Penagih**: One-to-One (optional)
2. **Penagih → Pelanggan**: One-to-Many
3. **Paket → Pelanggan**: One-to-Many
4. **Pelanggan → Pembayaran**: One-to-Many
5. **Paket → Pembayaran**: One-to-Many (optional)
6. **Penagih → Pembayaran**: One-to-Many (optional)

## Business Rules

### Payment Code Generation
- Format: `{PREFIX}{YYYY}{MM}{DD}{XXX}`
- Example: `PAY20250905123`
- Must be unique across all payments

### Customer Status
- `aktif`: Customer is active and receiving service
- `isolir`: Customer is isolated (inactive)
- `bayar double`: Customer needs to pay double (suspended for non-payment)

### Payment Status
- `belum_bayar`: Payment is pending
- `lunas`: Payment is completed

## Data Integrity

### Constraints
1. **Unique Constraints**:
   - User email must be unique
   - Customer PPPoE must be unique
   - Payment code must be unique
   - One payment per customer per month/year

2. **Foreign Key Constraints**:
   - All foreign keys have proper referential integrity
   - Cascade deletes where appropriate
   - Set null for optional relationships

3. **Check Constraints**:
   - Payment day must be between 1-31
   - Billing month must be between 1-12
   - Amounts must be positive

### Indexes for Performance
- All foreign keys are indexed
- Frequently queried columns are indexed
- Composite indexes for common query patterns
- Full-text indexes for search functionality

## Migration Strategy

### Optimized Migration Structure
- **Total Migrations**: 16 files (reduced from 28 files)
- **Core Tables**: 4 main business tables
- **Supporting Tables**: 12 additional tables for system functionality
- **All migrations tested and verified for VPS deployment**

### For New Installations
1. Run all migrations in order (16 files)
2. Run seeders to populate initial data
3. Verify data integrity

### For Existing Installations
1. Run pending migrations (optimized and consolidated)
2. Verify data integrity
3. Test application functionality

### Migration Optimization Summary
- ✅ Removed 12 redundant migration files
- ✅ Consolidated table structure changes
- ✅ Maintained data integrity and relationships
- ✅ Fixed missing timestamps in company_profiles table
- ✅ All migrations tested with `migrate:fresh --seed`

## Backup and Recovery

### Backup Strategy
- Daily automated backups
- Weekly full database dumps
- Monthly archive backups
- Backup history tracking

### Recovery Procedures
1. Restore from latest backup
2. Run pending migrations
3. Verify data integrity
4. Test application functionality

## Performance Considerations

### Query Optimization
- Use eager loading for relationships
- Implement proper indexing
- Use database views for complex queries
- Cache frequently accessed data

### Monitoring
- Monitor slow queries
- Track database size growth
- Monitor index usage
- Regular performance analysis

## Security Considerations

### Data Protection
- Encrypt sensitive data
- Implement proper access controls
- Regular security audits
- Backup encryption

### Access Control
- Role-based permissions
- Audit trail for all changes
- Secure backup storage
- Regular access reviews

---

*Last Updated: September 2025*  
*Version: 2.3.0 - Optimized for VPS Deployment*
