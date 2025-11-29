# WiFi Customer Management System - API Documentation

**Last Updated**: November 2024  
**Version**: 4.0.0

## Overview
API lengkap untuk sistem manajemen pelanggan WiFi yang mendukung:
- Customer Self-Service Portal (Mobile/Web App)
- Payment Gateway dan WhatsApp automation
- Ticket System untuk customer support
- Payment Proof validation
- Admin management integration


**Base URL:** `http://your-domain.com/api/v1`

**Development (Herd):** `http://backend-wifi.test/api/v1`
**Production:** `https://your-domain.com/api/v1`

## Authentication
API menggunakan Laravel Sanctum untuk authentication. Untuk endpoint yang memerlukan authentication, sertakan header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Rate Limiting
- **Public endpoints:** 60 requests per minute per IP
- **Authenticated endpoints:** 60 requests per minute per user

## Response Format
Semua response menggunakan format JSON dengan struktur:
```json
{
    "success": true|false,
    "message": "Deskripsi response",
    "data": {...},
    "pagination": {...} // untuk endpoint dengan pagination
}
```

---

## Public Endpoints (No Authentication Required)

### 1. Health Check
**GET** `/health`

Check status API dan server.

**Response:**
```json
{
    "status": "ok",
    "timestamp": "2025-01-07T10:30:00.000000Z",
    "version": "1.0.0"
}
```

### 2. Payment Gateway API

#### Check Customer Bills
**POST** `/payment/check-bills`

Cek tagihan pelanggan berdasarkan PPPoE, No HP, atau Kode Pembayaran.

**Request Body:**
```json
{
    "no_hp": "087828253777",               // Recommended: Nomor HP (paling mudah)
    "pppoe": "asep_saepudin_perum271224",  // Alternative: PPPoE username
    "kode_pembayaran": "PAY202501071234"   // Optional: Kode pembayaran spesifik
}
```

**Note:** 
- **No HP** - Paling mudah untuk customer (Recommended)
- **PPPoE** - Untuk customer yang tahu username mereka
- **Kode Pembayaran** - Untuk cek tagihan spesifik (setelah generate tagihan)
- Minimal harus ada 1 identifier

**Response:**
```json
{
    "success": true,
    "message": "Data tagihan ditemukan",
    "data": {
        "pelanggan_id": 1,
        "nama_pelanggan": "John Doe",
        "pppoe": "pelanggan123",
        "no_hp": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "nama_paket": "Paket 10 Mbps",
        "harga_paket": 150000,
        "total_unpaid_bills": 2,
        "total_amount_due": 300000,
        "latest_bill_code": "PAY202501071234",
        "unpaid_bills": [
            {
                "kode_pembayaran": "PAY202501071234",
                "periode": "1/2025",
                "status": "0",
                "harga": 150000,
                "tanggal_tagihan": "07/01/2025",
                "tanggal_jatuh_tempo": "07/02/2025",
                "is_overdue": false,
                "days_overdue": 0
            }
        ]
    }
}
```

#### Check Payment
**POST** `/payment/check`

Cek status pembayaran berdasarkan kode pembayaran.

**Request Body:**
```json
{
    "kode_pembayaran": "PAY00010012025"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Data tagihan ditemukan",
    "data": {
        "kode_pembayaran": "PAY00010012025",
        "nama_pelanggan": "John Doe",
        "no_hp": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "nama_paket": "Paket 150MB",
        "harga_paket": 150000,
        "periode": "1/2025",
        "status": "belum_lunas",
        "tanggal_tagihan": "07/01/2025",
        "tanggal_jatuh_tempo": "07/02/2025",
        "is_overdue": false,
        "days_overdue": 0,
        "tanggal_bayar": null,
        "keterangan": null
    }
}
```

**Response Error:**
```json
{
    "success": false,
    "message": "Kode pembayaran tidak ditemukan",
    "data": null
}
```

#### Verify Payment
**POST** `/payment/verify`

Verifikasi pembayaran dari payment gateway.

**Request Body:**
```json
{
    "kode_pembayaran": "PAY00010012025",
    "amount": 150000,
    "payment_method": "Credit Card",
    "transaction_id": "TXN123456789",
    "notes": "Pembayaran via Payment Gateway"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Pembayaran berhasil diverifikasi",
    "data": {
        "kode_pembayaran": "PAY00010012025",
        "status": "lunas",
        "tanggal_bayar": "07/01/2025 10:30",
        "amount": 150000,
        "transaction_id": "TXN123456789",
        "payment_method": "Credit Card"
    }
}
```

#### Payment History
**POST** `/payment/history`

Ambil riwayat pembayaran berdasarkan nomor HP.

**Request Body:**
```json
{
    "no_hp": "081234567890"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Riwayat pembayaran ditemukan",
    "data": {
        "nama_pelanggan": "John Doe",
        "no_hp": "081234567890",
        "alamat": "Jl. Contoh No. 123",
        "nama_paket": "Paket 150MB",
        "harga_paket": 150000,
        "payment_history": [
            {
                "kode_pembayaran": "PAY00010012025",
                "periode": "1/2025",
                "status": "lunas",
                "harga": 150000,
                "tanggal_tagihan": "07/01/2025",
                "tanggal_jatuh_tempo": "07/02/2025",
                "is_overdue": false,
                "tanggal_bayar": "07/01/2025 10:30"
            }
        ]
    }
}
```

### 3. WhatsApp API

#### Send Payment Code
**POST** `/whatsapp/send-payment-code`

Kirim kode pembayaran via WhatsApp.

**Request Body:**
```json
{
    "kode_pembayaran": "PAY00010012025",
    "phone_number": "081234567890"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Pesan WhatsApp berhasil dikirim",
    "data": {
        "message_id": "MSG123456789",
        "status": "sent",
        "phone_number": "081234567890",
        "kode_pembayaran": "PAY00010012025"
    }
}
```

#### Send Reminder
**POST** `/whatsapp/send-reminder`

Kirim reminder pembayaran via WhatsApp.

**Request Body:**
```json
{
    "kode_pembayaran": "PAY00010012025",
    "phone_number": "081234567890",
    "message_type": "overdue"
}
```

#### Get Message Status
**GET** `/whatsapp/status/{message_id}`

Cek status pesan WhatsApp.

**Response Success:**
```json
{
    "success": true,
    "message": "Status pesan berhasil diambil",
    "data": {
        "message_id": "MSG123456789",
        "status": "delivered",
        "sent_at": "2025-01-07T10:30:00.000000Z",
        "delivered_at": "2025-01-07T10:30:05.000000Z"
    }
}
```

---

## Protected Endpoints (Authentication Required)

### 1. Dashboard API

#### Get Statistics
**GET** `/dashboard/statistics`

Ambil statistik dashboard.

**Response:**
```json
{
    "success": true,
    "message": "Statistik dashboard berhasil diambil",
    "data": {
        "overview": {
            "total_customers": 150,
            "active_customers": 140,
            "inactive_customers": 10,
            "total_packages": 5,
            "active_packages": 4,
            "total_collectors": 3
        },
        "payments": {
            "total_payments": 500,
            "paid_payments": 450,
            "unpaid_payments": 50,
            "overdue_payments": 10,
            "recent_payments": 25,
            "current_month_payments": 150
        },
        "revenue": {
            "total_revenue": 67500000,
            "unpaid_amount": 7500000,
            "current_month_revenue": 22500000,
            "payment_rate": 90.0
        }
    }
}
```

#### Get Recent Activities
**GET** `/dashboard/recent-activities`

Ambil aktivitas terbaru.

#### Get Monthly Revenue
**GET** `/dashboard/monthly-revenue`

Ambil data revenue bulanan untuk chart.

### 2. Customer Management API

#### Get All Customers
**GET** `/customers`

Ambil daftar pelanggan dengan pagination dan filter.

**Query Parameters:**
- `search` - Pencarian nama/pppoe/no_hp/alamat
- `paket_id` - Filter berdasarkan paket
- `penagih_id` - Filter berdasarkan penagih
- `status` - Filter berdasarkan status (aktif/isolir)
- `per_page` - Jumlah data per halaman (default: 15)

**Response:**
```json
{
    "success": true,
    "message": "Data pelanggan berhasil diambil",
    "data": [
        {
            "id": 1,
            "nama": "John Doe",
            "pppoe": "john.doe",
            "no_hp": "081234567890",
            "alamat": "Jl. Contoh No. 123",
            "paket": {
                "id": 1,
                "nama_paket": "Paket 150MB",
                "harga": 150000
            },
            "penagih": {
                "id": 1,
                "nama": "Penagih 1"
            },
            "status": "aktif",
            "tanggal_pembayaran": 10,
            "created_at": "07/01/2025 10:30"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150,
        "from": 1,
        "to": 15
    }
}
```

#### Get Customer by ID
**GET** `/customers/{id}`

#### Create Customer
**POST** `/customers`

**Request Body:**
```json
{
    "nama": "John Doe",
    "pppoe": "john.doe",
    "no_hp": "081234567890",
    "alamat": "Jl. Contoh No. 123",
    "paket_id": 1,
    "penagih_id": 1,
    "tanggal_pembayaran": 10,
    "status": "aktif"
}
```

#### Update Customer
**PUT** `/customers/{id}`

#### Delete Customer
**DELETE** `/customers/{id}`

#### Get Customer Payment History
**GET** `/customers/{id}/payment-history`

### 3. Payment Management API

#### Get All Payments
**GET** `/payments`

**Query Parameters:**
- `search` - Pencarian kode pembayaran/nama pelanggan
- `status` - Filter berdasarkan status (lunas/belum_lunas)
- `bulan` - Filter berdasarkan bulan
- `tahun` - Filter berdasarkan tahun
- `penagih_id` - Filter berdasarkan penagih
- `per_page` - Jumlah data per halaman

#### Get Payment by ID
**GET** `/payments/{id}`

#### Update Payment Status
**PUT** `/payments/{id}/status`

**Request Body:**
```json
{
    "status": "lunas",
    "keterangan": "Pembayaran diterima"
}
```

#### Mark Payment as Paid
**PUT** `/payments/{id}/mark-paid`

#### Delete Payment
**DELETE** `/payments/{id}`

#### Generate Payments
**POST** `/payments/generate`

Generate tagihan untuk semua pelanggan aktif.

**Request Body:**
```json
{
    "bulan": 1,
    "tahun": 2025
}
```

### 4. Package Management API

#### Get All Packages
**GET** `/packages`

#### Create Package
**POST** `/packages`

#### Update Package
**PUT** `/packages/{id}`

#### Delete Package
**DELETE** `/packages/{id}`

#### Get Package Statistics
**GET** `/packages/{id}/statistics`

### 5. Report API

#### Get Revenue Report
**GET** `/reports/revenue`

**Query Parameters:**
- `start_date` - Tanggal mulai (YYYY-MM-DD)
- `end_date` - Tanggal akhir (YYYY-MM-DD)
- `bulan` - Filter berdasarkan bulan
- `tahun` - Filter berdasarkan tahun
- `penagih_id` - Filter berdasarkan penagih

#### Get Expense Report
**GET** `/reports/expenses`

#### Get Profit/Loss Report
**GET** `/reports/profit-loss`

### 6. User Management API

#### Get All Users
**GET** `/users`

#### Create User
**POST** `/users`

#### Update User
**PUT** `/users/{id}`

#### Delete User
**DELETE** `/users/{id}`

#### Get All Roles
**GET** `/users/roles/list`

#### Get All Permissions
**GET** `/users/permissions/list`

### 7. User Profile

#### Get Profile
**GET** `/profile`

Ambil data profil user yang sedang login.

---

## Error Codes

| Code | Description |
|------|-------------|
| `UNAUTHORIZED` | Token tidak valid atau tidak ada |
| `RATE_LIMIT_EXCEEDED` | Melebihi batas request |
| `PAYMENT_NOT_FOUND` | Kode pembayaran tidak ditemukan |
| `PAYMENT_ALREADY_PAID` | Pembayaran sudah lunas |
| `AMOUNT_MISMATCH` | Jumlah pembayaran tidak sesuai |
| `VALIDATION_ERROR` | Error validasi input |

---

## Testing

Gunakan script testing yang tersedia:
```bash
php test_api.php
```

Atau gunakan tools seperti Postman, Insomnia, atau curl untuk testing manual.

### Contoh Testing dengan cURL

#### Health Check
```bash
curl -X GET "http://localhost:8000/api/v1/health"
```

#### Check Payment
```bash
curl -X POST "http://localhost:8000/api/v1/payment/check" \
  -H "Content-Type: application/json" \
  -d '{"kode_pembayaran": "PAY00010012025"}'
```

#### Verify Payment
```bash
curl -X POST "http://localhost:8000/api/v1/payment/verify" \
  -H "Content-Type: application/json" \
  -d '{
    "kode_pembayaran": "PAY00010012025",
    "amount": 150000,
    "payment_method": "Credit Card",
    "transaction_id": "TXN123456789"
  }'
```

---

## Integration Examples

### Payment Gateway Integration

```javascript
// Check payment
const checkPayment = async (kodePembayaran) => {
    const response = await fetch('/api/v1/payment/check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ kode_pembayaran })
    });
    return await response.json();
};

// Verify payment
const verifyPayment = async (kodePembayaran, amount, transactionId) => {
    const response = await fetch('/api/v1/payment/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            kode_pembayaran,
            amount,
            transaction_id: transactionId,
            payment_method: 'Credit Card'
        })
    });
    return await response.json();
};
```

### WhatsApp Integration

```javascript
// Send payment code
const sendPaymentCode = async (kodePembayaran, phoneNumber) => {
    const response = await fetch('/api/v1/whatsapp/send-payment-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            kode_pembayaran,
            phone_number: phoneNumber
        })
    });
    return await response.json();
};
```

---

## Customer Self-Service API

### Customer Authentication

#### 1. Customer Login
**POST** `/customer/auth/login`

Login untuk customer menggunakan nomor HP atau PPPoE dan password.

**Request Body:**
```json
{
    "username": "081234567890",
    "password": "password123"
}
```

**Note:** `username` bisa berupa nomor HP atau PPPoE username.

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "token": "1|abc123...",
    "customer": {
        "id": 1,
        "nama": "John Doe",
        "no_hp": "081234567890",
        "email": "john@example.com",
        "alamat": "Jl. Example No. 123",
        "is_default_password": false,
        "last_login_at": "2025-01-14T10:30:00.000000Z"
    }
}
```

#### 2. Customer Logout
**POST** `/customer/auth/logout`

Logout customer dan revoke token.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

#### 3. Get Customer Profile
**GET** `/customer/auth/me`

Get informasi customer yang sedang login.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama": "John Doe",
        "no_hp": "081234567890",
        "email": "john@example.com",
        "alamat": "Jl. Example No. 123",
        "paket": {
            "id": 1,
            "nama_paket": "Paket 10 Mbps",
            "harga": 150000
        },
        "penagih": {
            "id": 1,
            "nama": "Penagih A"
        }
    }
}
```

#### 4. Change Password
**POST** `/customer/auth/change-password`

Ubah password customer.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password berhasil diubah"
}
```

### Customer Payment Management

#### 1. Get Unpaid Bills
**GET** `/customer/payment/bills`

Get daftar tagihan yang belum dibayar.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode_pembayaran": "PAY-20250114-001",
            "harga_paket": 150000,
            "tanggal_jatuh_tempo": "2025-01-20",
            "status": "belum_lunas",
            "paket": {
                "nama_paket": "Paket 10 Mbps"
            }
        }
    ]
}
```

#### 2. Get Payment History
**GET** `/customer/payment/history`

Get riwayat pembayaran customer.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `page` (optional): Halaman (default: 1)
- `per_page` (optional): Jumlah data per halaman (default: 15)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode_pembayaran": "PAY-20250114-001",
            "harga_paket": 150000,
            "tanggal_bayar": "2025-01-14T10:30:00.000000Z",
            "status": "lunas",
            "paket": {
                "nama_paket": "Paket 10 Mbps"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

#### 3. Upload Payment Proof
**POST** `/customer/payment/upload-proof`

Upload bukti pembayaran.

**Headers:** `Authorization: Bearer {token}`

**Request Body (multipart/form-data):**
```
pembayaran_id: 1
proof_file: [file]
```

**Response:**
```json
{
    "success": true,
    "message": "Bukti pembayaran berhasil diupload",
    "data": {
        "payment_proof_id": 1,
        "status": "pending",
        "file_url": "http://domain.com/storage/payment_proofs/file.jpg"
    }
}
```

#### 4. Send Payment Proof to WhatsApp
**POST** `/customer/payment/send-wa`

Kirim bukti pembayaran via WhatsApp.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "pembayaran_id": 1,
    "message": "Bukti pembayaran untuk kode PAY-20250114-001"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bukti pembayaran berhasil dikirim via WhatsApp"
}
```

#### 5. Get Payment Status
**GET** `/customer/payment/status/{id}`

Get status pembayaran dan bukti pembayaran.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "pembayaran": {
            "id": 1,
            "kode_pembayaran": "PAY-20250114-001",
            "status": "lunas",
            "tanggal_bayar": "2025-01-14T10:30:00.000000Z"
        },
        "payment_proofs": [
            {
                "id": 1,
                "status": "verified",
                "file_url": "http://domain.com/storage/payment_proofs/file.jpg",
                "admin_notes": "Pembayaran sudah diverifikasi",
                "verified_at": "2025-01-14T11:00:00.000000Z"
            }
        ]
    }
}
```

### Customer Support (Ticket System)

#### 1. Get Customer Tickets
**GET** `/customer/support/tickets`

Get daftar ticket customer.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional): Filter by status (open, in_progress, resolved, closed)
- `page` (optional): Halaman (default: 1)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode_ticket": "TKT-20250114-001",
            "judul": "Internet lambat",
            "kategori": "technical",
            "prioritas": "medium",
            "status": "open",
            "created_at": "2025-01-14T10:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

#### 2. Create Ticket
**POST** `/customer/support/tickets`

Buat ticket baru.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "judul": "Internet lambat",
    "deskripsi": "Internet saya sangat lambat sejak kemarin",
    "kategori": "technical",
    "prioritas": "medium"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Ticket berhasil dibuat",
    "data": {
        "id": 1,
        "kode_ticket": "TKT-20250114-001",
        "judul": "Internet lambat",
        "status": "open"
    }
}
```

#### 3. Get Ticket Detail
**GET** `/customer/support/tickets/{id}`

Get detail ticket.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "kode_ticket": "TKT-20250114-001",
        "judul": "Internet lambat",
        "deskripsi": "Internet saya sangat lambat sejak kemarin",
        "kategori": "technical",
        "prioritas": "medium",
        "status": "open",
        "created_at": "2025-01-14T10:30:00.000000Z",
        "comments": [
            {
                "id": 1,
                "comment": "Terima kasih atas laporan Anda. Tim kami akan segera mengecek.",
                "created_at": "2025-01-14T11:00:00.000000Z",
                "user": {
                    "name": "Admin Support"
                }
            }
        ],
        "attachments": []
    }
}
```

#### 4. Add Comment to Ticket
**POST** `/customer/support/tickets/{id}/comments`

Tambah komentar ke ticket.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "comment": "Masalah masih berlanjut sampai sekarang"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Komentar berhasil ditambahkan"
}
```

#### 5. Upload Attachment to Ticket
**POST** `/customer/support/tickets/{id}/attachments`

Upload file attachment ke ticket.

**Headers:** `Authorization: Bearer {token}`

**Request Body (multipart/form-data):**
```
attachment: [file]
```

**Response:**
```json
{
    "success": true,
    "message": "Attachment berhasil diupload",
    "data": {
        "attachment_id": 1,
        "file_url": "http://domain.com/storage/ticket_attachments/file.jpg"
    }
}
```

#### 6. Rate Ticket Resolution
**POST** `/customer/support/tickets/{id}/rate`

Beri rating untuk resolusi ticket.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "rating": 5,
    "customer_feedback": "Masalah sudah teratasi dengan baik"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Rating berhasil disimpan"
}
```

### Customer Profile Management

#### 1. Get Customer Profile
**GET** `/customer/profile/`

Get profil customer.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama": "John Doe",
        "no_hp": "081234567890",
        "email": "john@example.com",
        "alamat": "Jl. Example No. 123",
        "paket": {
            "id": 1,
            "nama_paket": "Paket 10 Mbps",
            "harga": 150000
        },
        "penagih": {
            "id": 1,
            "nama": "Penagih A"
        }
    }
}
```

#### 2. Update Customer Profile
**PUT** `/customer/profile/`

Update profil customer.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "nama": "John Doe Updated",
    "email": "john.updated@example.com",
    "alamat": "Jl. Example Updated No. 123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Profil berhasil diperbarui",
    "data": {
        "id": 1,
        "nama": "John Doe Updated",
        "email": "john.updated@example.com",
        "alamat": "Jl. Example Updated No. 123"
    }
}
```

#### 3. Change Password
**POST** `/customer/profile/change-password`

Ubah password customer.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password berhasil diubah"
}
```

#### 4. Get Customer Statistics
**GET** `/customer/profile/statistics`

Get statistik customer.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "total_payments": 12,
        "paid_payments": 10,
        "unpaid_payments": 2,
        "total_tickets": 3,
        "resolved_tickets": 2,
        "open_tickets": 1,
        "average_rating": 4.5
    }
}
```

### Admin Customer Management

#### 1. Generate Default Password
**POST** `/admin/customer/generate-password`

Generate password default (123456) untuk customer (Admin only).

**Headers:** `Authorization: Bearer {admin_token}`

**Request Body:**
```json
{
    "pelanggan_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password default (123456) berhasil dibuat",
    "data": {
        "default_password": "PWD123456"
    }
}
```

---

## Support

Untuk pertanyaan atau bantuan teknis, silakan hubungi tim development.
