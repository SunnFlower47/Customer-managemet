<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;

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

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {

    // Payment Gateway API (Public)
    Route::prefix('payment')->group(function () {
        Route::post('/check', [PaymentController::class, 'checkPayment']);
        Route::post('/check-bills', [PaymentController::class, 'checkCustomerBills']);
        Route::post('/verify', [PaymentController::class, 'verifyPayment']);
        Route::post('/history', [PaymentController::class, 'getPaymentHistory']);
    });

    // WhatsApp API (Public)
    Route::prefix('whatsapp')->group(function () {
        Route::post('/send-payment-code', [WhatsAppController::class, 'sendPaymentCode']);
        Route::post('/send-reminder', [WhatsAppController::class, 'sendReminder']);
        Route::get('/status/{message_id}', [WhatsAppController::class, 'getMessageStatus']);
    });

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    });
});

// Protected API routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // Dashboard API
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'statistics']);
        Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/monthly-revenue', [DashboardController::class, 'monthlyRevenue']);
    });

    // Customer Management API
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
        Route::get('/{id}/payment-history', [CustomerController::class, 'paymentHistory']);
    });

    // Payment Management API
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentApiController::class, 'index']);
        Route::get('/{id}', [PaymentApiController::class, 'show']);
        Route::put('/{id}/status', [PaymentApiController::class, 'updateStatus']);
        Route::put('/{id}/mark-paid', [PaymentApiController::class, 'markPaid']);
        Route::delete('/{id}', [PaymentApiController::class, 'destroy']);
        Route::post('/generate', [PaymentApiController::class, 'generatePayments']);
    });

    // Package Management API
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::post('/', [PackageController::class, 'store']);
        Route::get('/{id}', [PackageController::class, 'show']);
        Route::put('/{id}', [PackageController::class, 'update']);
        Route::delete('/{id}', [PackageController::class, 'destroy']);
        Route::get('/{id}/statistics', [PackageController::class, 'statistics']);
    });

    // Report API
    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [ReportController::class, 'revenue']);
        Route::get('/expenses', [ReportController::class, 'expenses']);
        Route::get('/profit-loss', [ReportController::class, 'profitLoss']);
    });

    // User Management API
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/roles/list', [UserController::class, 'roles']);
        Route::get('/permissions/list', [UserController::class, 'permissions']);
    });

    // User profile
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->roles->first()->name ?? 'No Role',
                'permissions' => $request->user()->getAllPermissions()->pluck('name')
            ]
        ]);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'available_endpoints' => [
            'public' => [
                'GET /api/v1/health',
                'GET /api/v1/payment/check/{kode_pembayaran}',
                'POST /api/v1/payment/verify',
                'GET /api/v1/payment/history/{kode_pembayaran}',
                'POST /api/v1/whatsapp/send-payment-code',
                'POST /api/v1/whatsapp/send-reminder',
                'GET /api/v1/whatsapp/status/{message_id}'
            ],
            'protected' => [
                'GET /api/v1/dashboard/statistics',
                'GET /api/v1/customers',
                'GET /api/v1/payments',
                'GET /api/v1/packages',
                'GET /api/v1/reports/revenue',
                'GET /api/v1/users',
                'GET /api/v1/profile'
            ]
        ]
    ], 404);
});

