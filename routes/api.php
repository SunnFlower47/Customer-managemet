<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerPaymentController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CustomerProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================================================
Route::prefix('v1')->group(function () {

    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'environment' => config('app.env'),
        ]);
    });

    // Customer Authentication (Public)
    // Rate limit: 20 requests per minute (more lenient for login)
    Route::prefix('customer/auth')->middleware(['api.rate:20,1'])->group(function () {
        Route::post('/login', [CustomerAuthController::class, 'login']);
    });

    // Payment Methods (Public for customers)
    Route::get('/payment-methods', function () {
        $methods = \App\Services\CacheService::getPaymentMethods();
        return response()->json([
            'success' => true,
            'data' => $methods ?? [
                'dana_phone' => null,
                'mandiri_account' => null,
                'mandiri_account_name' => null,
                'payment_whatsapp' => null,
                'company_name' => null,
            ],
        ]);
    });
});

// ============================================================================
// CUSTOMER API ROUTES (Customer Authentication Required)
// ============================================================================
Route::prefix('v1')->middleware(['customer.auth', 'api.rate:60,1'])->group(function () {

    // Customer Authentication Management
    Route::prefix('customer/auth')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/me', [CustomerAuthController::class, 'me']);
        Route::post('/change-password', [CustomerAuthController::class, 'changePassword']);
    });

    // Customer Profile Management
    Route::prefix('customer/profile')->group(function () {
        Route::get('/', [CustomerProfileController::class, 'show']);
        Route::put('/', [CustomerProfileController::class, 'update']);
        Route::post('/change-password', [CustomerProfileController::class, 'changePassword']);
        Route::get('/statistics', [CustomerProfileController::class, 'statistics']);
    });

    // Customer Payment Management
    Route::prefix('customer/payment')->group(function () {
        Route::get('/bills', [CustomerPaymentController::class, 'getUnpaidBills']);
        Route::get('/history', [CustomerPaymentController::class, 'getPaymentHistory']);
        Route::post('/upload-proof', [CustomerPaymentController::class, 'uploadPaymentProof']);
        Route::post('/send-wa', [CustomerPaymentController::class, 'sendToWhatsApp']);
        Route::get('/status/{id}', [CustomerPaymentController::class, 'getPaymentStatus']);
    });

    // Customer Support/Ticket Management
    Route::prefix('customer/support')->group(function () {
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::get('/tickets/{id}', [TicketController::class, 'show']);
        Route::post('/tickets/{id}/comments', [TicketController::class, 'addComment']);
        Route::post('/tickets/{id}/attachments', [TicketController::class, 'uploadAttachment']);
        Route::post('/tickets/{id}/rate', [TicketController::class, 'rateResolution']);
    });
});

// ============================================================================
// ADMIN API ROUTES (Sanctum Authentication Required)
// ============================================================================
Route::prefix('v1')->middleware(['auth:sanctum', 'api.rate:60,1'])->group(function () {

    // Admin - Payment Gateway API
    Route::prefix('payment')->group(function () {
        Route::post('/check', [PaymentController::class, 'checkPayment']);
        Route::post('/check-bills', [PaymentController::class, 'checkCustomerBills']);
        Route::post('/verify', [PaymentController::class, 'verifyPayment']);
        Route::post('/history', [PaymentController::class, 'getPaymentHistory']);
    });

    // Admin - WhatsApp API
    Route::prefix('whatsapp')->group(function () {
        Route::post('/send-payment-code', [WhatsAppController::class, 'sendPaymentCode']);
        Route::post('/send-reminder', [WhatsAppController::class, 'sendReminder']);
        Route::get('/status/{message_id}', [WhatsAppController::class, 'getMessageStatus']);
    });

    // Admin - Dashboard API
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'statistics']);
        Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/monthly-revenue', [DashboardController::class, 'monthlyRevenue']);
    });

    // Admin - Customer Management API
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
        Route::get('/{id}/payment-history', [CustomerController::class, 'paymentHistory']);
    });

    // Admin - Payment Management API
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentApiController::class, 'index']);
        Route::get('/{id}', [PaymentApiController::class, 'show']);
        Route::put('/{id}/status', [PaymentApiController::class, 'updateStatus']);
        Route::put('/{id}/mark-paid', [PaymentApiController::class, 'markPaid']);
        Route::delete('/{id}', [PaymentApiController::class, 'destroy']);
        Route::post('/generate', [PaymentApiController::class, 'generatePayments']);
    });

    // Admin - Package Management API
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::post('/', [PackageController::class, 'store']);
        Route::get('/{id}', [PackageController::class, 'show']);
        Route::put('/{id}', [PackageController::class, 'update']);
        Route::delete('/{id}', [PackageController::class, 'destroy']);
        Route::get('/{id}/statistics', [PackageController::class, 'statistics']);
    });

    // Admin - Report API
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [ReportController::class, 'revenue']);
        Route::get('/expenses', [ReportController::class, 'expenses']);
        Route::get('/profit-loss', [ReportController::class, 'profitLoss']);
    });

    // Admin - User Management API
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/roles/list', [UserController::class, 'roles']);
        Route::get('/permissions/list', [UserController::class, 'permissions']);
    });

    // Admin - Customer Password Generation
    Route::prefix('admin')->group(function () {
        Route::post('/customer/generate-password', [CustomerAuthController::class, 'generateDefaultPassword']);
    });

    // OLT Real-time API (requires authentication)
    Route::prefix('olts')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/{olt}/realtime', [\App\Http\Controllers\Api\OltApiController::class, 'getRealtimeData']);
        Route::get('/realtime/all', [\App\Http\Controllers\Api\OltApiController::class, 'getAllRealtimeData']);
    });

    Route::prefix('onus')->middleware(['auth:web'])->group(function () {
        Route::get('/{onu}/traffic', [\App\Http\Controllers\Api\OltApiController::class, 'getOnuTraffic'])->name('onus.traffic');
    });
});

// ============================================================================
// FALLBACK ROUTE
// ============================================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error_code' => 'ENDPOINT_NOT_FOUND',
        'documentation' => config('app.url') . '/api/documentation'
    ], 404);
});
