# 🔒 API Security Guide for GitHub

## 🚨 CRITICAL: Never Commit Sensitive Data

### ❌ What NOT to commit to GitHub:
- `.env` files (contains database passwords, API keys)
- `config/database.php` with real credentials
- API tokens and secrets
- Private keys and certificates
- Production database dumps
- Backup files with real data

### ✅ What IS safe to commit:
- `.env.example` (template with placeholder values)
- `config/database.php` (using env() functions)
- Documentation and README files
- Source code (without hardcoded secrets)
- Migration files
- Seeder files (with fake data only)

## 🛡️ Security Implementation

### 1. Environment Variables Setup

Create `.env.example` file:
```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wifi_billing
DB_USERNAME=your_username
DB_PASSWORD=your_password

# API Security
API_RATE_LIMIT=60
API_TOKEN_EXPIRY=1440

# WhatsApp API (Optional)
WHATSAPP_API_URL=https://api.whatsapp.com
WHATSAPP_API_TOKEN=your_whatsapp_token
WHATSAPP_PHONE_NUMBER=+6281234567890

# Payment Gateway (Optional)
PAYMENT_GATEWAY_URL=https://api.payment-gateway.com
PAYMENT_GATEWAY_TOKEN=your_payment_gateway_token
PAYMENT_GATEWAY_SECRET=your_payment_gateway_secret
```

### 2. .gitignore Configuration

Ensure `.gitignore` includes:
```gitignore
# Environment files
.env
.env.local
.env.production
.env.staging

# Database files
*.sql
*.dump
database/backups/
storage/backups/

# Logs
storage/logs/
*.log

# Cache
bootstrap/cache/
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/

# Uploads
storage/app/public/uploads/
public/uploads/

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS files
.DS_Store
Thumbs.db
```

### 3. API Authentication

#### Token-Based Authentication
```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('customers', CustomerController::class);
});
```

#### Rate Limiting
```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'api' => [
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

// config/rate-limiting.php
'api' => [
    'driver' => 'throttle',
    'key' => fn ($request) => $request->user()?->id ?: $request->ip(),
    'max_attempts' => 60,
    'decay_minutes' => 1,
],
```

### 4. API Security Headers

```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    
    return $response;
}
```

### 5. Input Validation & Sanitization

```php
// API Request Validation
class PaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:pelanggans,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,lunas',
            'notes' => 'nullable|string|max:500',
        ];
    }
    
    public function messages()
    {
        return [
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Customer not found',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be positive',
        ];
    }
}
```

### 6. API Response Formatting

```php
// app/Http/Controllers/Api/BaseController.php
class BaseController extends Controller
{
    protected function successResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }
    
    protected function errorResponse($message, $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ], $code);
    }
}
```

## 🔐 Production Security Checklist

### Before Deploying to GitHub:

1. **✅ Remove all sensitive data**
   - No real database credentials
   - No API keys or tokens
   - No production URLs
   - No real phone numbers or emails

2. **✅ Use environment variables**
   - All sensitive config in `.env`
   - Use `env()` helper in config files
   - Provide `.env.example` template

3. **✅ Implement proper authentication**
   - API token authentication
   - Rate limiting
   - Input validation
   - CSRF protection for web routes

4. **✅ Add security headers**
   - CORS configuration
   - Security headers middleware
   - HTTPS enforcement

5. **✅ Test security measures**
   - Test API endpoints
   - Verify rate limiting works
   - Check authentication requirements
   - Validate input sanitization

## 🚀 Safe GitHub Deployment

### Repository Structure:
```
wifi-billing-system/
├── backend/
│   ├── .env.example          # ✅ Safe to commit
│   ├── .gitignore           # ✅ Safe to commit
│   ├── README.md            # ✅ Safe to commit
│   ├── docs/                # ✅ Safe to commit
│   │   ├── API_SECURITY_GUIDE.md
│   │   └── DEPLOYMENT_GUIDE.md
│   ├── app/                 # ✅ Safe to commit
│   ├── config/              # ✅ Safe to commit (using env())
│   ├── database/            # ✅ Safe to commit
│   └── routes/              # ✅ Safe to commit
└── .github/                 # ✅ Safe to commit
    └── workflows/           # CI/CD workflows
```

### Installation Instructions for Users:

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/wifi-billing-system.git
cd wifi-billing-system/backend
```

2. **Copy environment template**
```bash
cp .env.example .env
```

3. **Configure your environment**
```bash
# Edit .env file with your actual values
nano .env
```

4. **Install dependencies**
```bash
composer install
npm install
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Run migrations**
```bash
php artisan migrate
php artisan db:seed
```

## 🔍 Security Monitoring

### Log Security Events:
```php
// Log API access attempts
Log::info('API Access', [
    'user_id' => $user->id,
    'endpoint' => $request->path(),
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now(),
]);
```

### Monitor for Suspicious Activity:
- Multiple failed login attempts
- Unusual API usage patterns
- Large file uploads
- Database backup/restore operations

## 📞 Support

For security questions or to report vulnerabilities:
- Create a private issue on GitHub
- Contact: security@bcmnet.com
- Follow responsible disclosure practices

---

**Remember: Security is an ongoing process, not a one-time setup!**
