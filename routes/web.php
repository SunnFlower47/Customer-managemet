<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\DashboardController as WebDashboardController;
use App\Http\Controllers\Web\PaketController as WebPaketController;
use App\Http\Controllers\Web\PenagihController as WebPenagihController;
use App\Http\Controllers\Web\PelangganController as WebPelangganController;
use App\Http\Controllers\Web\PembayaranController as WebPembayaranController;
use App\Http\Controllers\Web\UserController as WebUserController;
use App\Http\Controllers\Web\AuditTrailController as WebAuditTrailController;
use App\Http\Controllers\Web\PengeluaranController as WebPengeluaranController;
use App\Http\Controllers\Web\LaporanController as WebLaporanController;
use App\Http\Controllers\Web\SettingController as WebSettingController;
use App\Http\Controllers\Web\SeoController as WebSeoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Service Worker Route
Route::get('/sw.js', function () {
    $content = file_get_contents(public_path('sw.js'));
    return Response::make($content, 200, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=3600',
    ]);
});

// Logo Route for Shared Hosting
Route::get('/logo/{filename}', function ($filename) {
    $companyProfile = \App\Models\CompanyProfile::first();
    
    if ($companyProfile && $companyProfile->logo_path) {
        $logoPath = storage_path('app/public/' . $companyProfile->logo_path);
        
        if (file_exists($logoPath)) {
            return Response::file($logoPath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
    }
    
    // Fallback to default icon
    return Response::file(public_path('icon-192x192.png'), [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('logo');

// Dynamic PWA Manifest - DISABLED to use static manifest.json
// Route::get('/manifest.json', function () {
//     $companyProfile = \App\Models\CompanyProfile::first();
//     $companyName = $companyProfile->display_name ?? 'BCM.net';
//     $companyShortName = $companyProfile->short_name ?? 'BCM.net';
//     $companyInitials = $companyProfile->initials ?? 'BCM';

//     return response()->json([
//         'name' => $companyName . ' - WiFi customer management',
//         'short_name' => $companyShortName,
//         'description' => 'Sistem manajemen billing WiFi ' . $companyName . ' dengan fitur PWA lengkap',
//         'start_url' => '/dashboard',
//         'display' => 'standalone',
//         'background_color' => '#ffffff',
//         'theme_color' => '#2563eb',
//         'orientation' => 'portrait-primary',
//         'scope' => '/',
//         'icons' => [
//             [
//                 'src' => '/icon-192x192.png',
//                 'sizes' => '192x192',
//                 'type' => 'image/png',
//                 'purpose' => 'any maskable'
//             ],
//             [
//                 'src' => '/icon-512x512.png',
//                 'sizes' => '512x512',
//                 'type' => 'image/png',
//                 'purpose' => 'any maskable'
//             ]
//         ],
//         'categories' => ['business', 'productivity', 'finance'],
//         'lang' => 'id',
//         'dir' => 'ltr'
//     ])->header('Content-Type', 'application/json');
// });

// SEO routes
Route::get('/sitemap.xml', [WebSeoController::class, 'sitemap']);
Route::get('/robots.txt', [WebSeoController::class, 'robots']);

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/server-time', function() {
        $now = now()->setTimezone('Asia/Jakarta');
        return response()->json([
            'time' => $now->format('d M Y H:i:s'),
            'timestamp' => $now->timestamp,
            'timezone' => 'Asia/Jakarta'
        ]);
    })->name('api.server-time');

    // Pakets
    Route::middleware(['permission:view-paket'])->group(function () {
        Route::get('/pakets', [WebPaketController::class, 'index'])->name('pakets.index');
    });

    Route::middleware(['permission:create-paket'])->group(function () {
        Route::get('/pakets/create', [WebPaketController::class, 'create'])->name('pakets.create');
        Route::post('/pakets', [WebPaketController::class, 'store'])->name('pakets.store');
    });

    Route::middleware(['permission:view-paket'])->group(function () {
        Route::get('/pakets/{paket}', [WebPaketController::class, 'show'])->name('pakets.show');
    });

    Route::middleware(['permission:edit-paket'])->group(function () {
        Route::get('/pakets/{paket}/edit', [WebPaketController::class, 'edit'])->name('pakets.edit');
        Route::put('/pakets/{paket}', [WebPaketController::class, 'update'])->name('pakets.update');
    });

    Route::middleware(['permission:delete-paket'])->group(function () {
        Route::delete('/pakets/{paket}', [WebPaketController::class, 'destroy'])->name('pakets.destroy');
    });

    // Penagihs
    Route::middleware(['permission:view-penagih'])->group(function () {
        Route::get('/penagihs', [WebPenagihController::class, 'index'])->name('penagihs.index');
    });

    Route::middleware(['permission:create-penagih'])->group(function () {
        Route::get('/penagihs/create', [WebPenagihController::class, 'create'])->name('penagihs.create');
        Route::post('/penagihs', [WebPenagihController::class, 'store'])->name('penagihs.store');
    });

    Route::middleware(['permission:view-penagih'])->group(function () {
        Route::get('/penagihs/{penagih}', [WebPenagihController::class, 'show'])->name('penagihs.show');
    });

    Route::middleware(['permission:edit-penagih'])->group(function () {
        Route::get('/penagihs/{penagih}/edit', [WebPenagihController::class, 'edit'])->name('penagihs.edit');
        Route::put('/penagihs/{penagih}', [WebPenagihController::class, 'update'])->name('penagihs.update');
    });

    Route::middleware(['permission:delete-penagih'])->group(function () {
        Route::delete('/penagihs/{penagih}', [WebPenagihController::class, 'destroy'])->name('penagihs.destroy');
    });

    // Pelanggans
    Route::middleware(['permission:view-pelanggan'])->group(function () {
        Route::get('/pelanggans', [WebPelangganController::class, 'index'])->name('pelanggans.index');
    });

    Route::middleware(['permission:create-pelanggan'])->group(function () {
        Route::get('/pelanggans/create', [WebPelangganController::class, 'create'])->name('pelanggans.create');
        Route::post('/pelanggans', [WebPelangganController::class, 'store'])->name('pelanggans.store');
    });

    Route::middleware(['permission:view-pelanggan'])->group(function () {
        Route::get('/pelanggans/{pelanggan}', [WebPelangganController::class, 'show'])->name('pelanggans.show');
    });

    Route::middleware(['permission:edit-pelanggan'])->group(function () {
        Route::get('/pelanggans/{pelanggan}/edit', [WebPelangganController::class, 'edit'])->name('pelanggans.edit');
        Route::put('/pelanggans/{pelanggan}', [WebPelangganController::class, 'update'])->name('pelanggans.update');
    });

    Route::middleware(['permission:delete-pelanggan'])->group(function () {
        Route::delete('/pelanggans/{pelanggan}', [WebPelangganController::class, 'destroy'])->name('pelanggans.destroy');
    });

    Route::middleware(['permission:export-pelanggan'])->group(function () {
        Route::get('/pelanggans/export/pdf', [WebPelangganController::class, 'exportPdf'])->name('pelanggans.export.pdf');
    });

    // Pembayarans
    Route::middleware(['permission:view-pembayaran'])->group(function () {
        Route::get('/pembayarans', [WebPembayaranController::class, 'index'])->name('pembayarans.index');
        Route::get('/pembayarans/{pembayaran}', [WebPembayaranController::class, 'show'])->name('pembayarans.show');
    });

    // Pembayaran create/store routes removed - payments are generated automatically

    Route::middleware(['permission:edit-pembayaran'])->group(function () {
        Route::get('/pembayarans/{pembayaran}/edit', [WebPembayaranController::class, 'edit'])->name('pembayarans.edit');
        Route::put('/pembayarans/{pembayaran}', [WebPembayaranController::class, 'update'])->name('pembayarans.update');
        Route::patch('/pembayarans/{pembayaran}/status', [WebPembayaranController::class, 'updateStatus'])->name('pembayarans.update-status');
        Route::patch('/pembayarans/{pembayaran}/mark-paid', [WebPembayaranController::class, 'markPaid'])->name('pembayarans.mark-paid');
    });

    Route::middleware(['permission:delete-pembayaran'])->group(function () {
        Route::delete('/pembayarans/{pembayaran}', [WebPembayaranController::class, 'destroy'])->name('pembayarans.destroy');
    });

    Route::middleware(['permission:export-pembayaran'])->group(function () {
        // Specific routes first (before parameterized routes)
        Route::get('/pembayarans/export/pdf', [WebPembayaranController::class, 'export'])->name('pembayarans.export');
        Route::get('/pembayarans/export/excel', [WebPembayaranController::class, 'exportExcel'])->name('pembayarans.export.excel');
        Route::get('/pembayarans/export/csv', [WebPembayaranController::class, 'exportCsv'])->name('pembayarans.export.csv');

        // Parameterized routes after specific routes
        Route::get('/pembayarans/{pembayaran}/invoice', [WebPembayaranController::class, 'invoice'])->name('pembayarans.invoice');
        Route::get('/pembayarans/{pembayaran}/invoice/pdf', [WebPembayaranController::class, 'invoicePdf'])->name('pembayarans.invoice.pdf');
        Route::get('/pembayarans/{pembayaran}/pdf', [WebPembayaranController::class, 'pdf'])->name('pembayarans.pdf');
        Route::get('/pembayarans/{pembayaran}/print-invoice', [WebPembayaranController::class, 'printInvoice'])->name('pembayarans.print-invoice');
    });

    // Generate bills route
    Route::middleware(['permission:create-pembayaran'])->group(function () {
        Route::post('/pembayarans/generate-bills', [WebPembayaranController::class, 'generateBills'])->name('pembayarans.generate-bills');
        Route::post('/run-smart-bills', [WebDashboardController::class, 'runSmartBills'])->name('run.smart.bills');
    });

    // Pengeluarans
    Route::middleware(['permission:view-pengeluaran'])->group(function () {
        Route::get('/pengeluarans', [WebPengeluaranController::class, 'index'])->name('pengeluarans.index');
    });

    Route::middleware(['permission:create-pengeluaran'])->group(function () {
        Route::get('/pengeluarans/create', [WebPengeluaranController::class, 'create'])->name('pengeluarans.create');
        Route::post('/pengeluarans', [WebPengeluaranController::class, 'store'])->name('pengeluarans.store');
    });

    Route::middleware(['permission:view-pengeluaran'])->group(function () {
        Route::get('/pengeluarans/{pengeluaran}', [WebPengeluaranController::class, 'show'])->name('pengeluarans.show');
    });

    Route::middleware(['permission:edit-pengeluaran'])->group(function () {
        Route::get('/pengeluarans/{pengeluaran}/edit', [WebPengeluaranController::class, 'edit'])->name('pengeluarans.edit');
        Route::put('/pengeluarans/{pengeluaran}', [WebPengeluaranController::class, 'update'])->name('pengeluarans.update');
    });

    Route::middleware(['permission:delete-pengeluaran'])->group(function () {
        Route::delete('/pengeluarans/{pengeluaran}', [WebPengeluaranController::class, 'destroy'])->name('pengeluarans.destroy');
    });

    Route::middleware(['permission:export-pengeluaran'])->group(function () {
        Route::get('/pengeluarans/export', [WebPengeluaranController::class, 'export'])->name('pengeluarans.export');
    });

    // Laporan
    Route::middleware(['permission:view-laporan-pendapatan|view-laporan-pengeluaran|view-laporan-laba-rugi'])->group(function () {
        Route::get('/laporan', [WebLaporanController::class, 'index'])->name('laporan.index');
    });

    Route::middleware(['permission:view-laporan-pendapatan'])->group(function () {
        Route::get('/laporan/pendapatan', [WebLaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
    });

    Route::middleware(['permission:view-laporan-pengeluaran'])->group(function () {
        Route::get('/laporan/pengeluaran', [WebLaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
    });

    Route::middleware(['permission:view-laporan-laba-rugi'])->group(function () {
        Route::get('/laporan/laba-rugi', [WebLaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
    });

    // Users
    Route::middleware(['permission:view-user'])->group(function () {
        Route::get('/users', [WebUserController::class, 'index'])->name('users.index');
    });

    Route::middleware(['permission:create-user'])->group(function () {
        Route::get('/users/create', [WebUserController::class, 'create'])->name('users.create');
        Route::post('/users', [WebUserController::class, 'store'])->name('users.store');
    });

    Route::middleware(['permission:view-user'])->group(function () {
        Route::get('/users/{user}', [WebUserController::class, 'show'])->name('users.show');
    });

    Route::middleware(['permission:edit-user'])->group(function () {
        Route::get('/users/{user}/edit', [WebUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [WebUserController::class, 'update'])->name('users.update');
    });

    Route::middleware(['permission:delete-user'])->group(function () {
        Route::delete('/users/{user}', [WebUserController::class, 'destroy'])->name('users.destroy');
    });

    // Audit Trails
    Route::middleware(['permission:view-audit-trail'])->group(function () {
        Route::get('/audit-trails', [WebAuditTrailController::class, 'index'])->name('audit-trails.index');
        Route::get('/audit-trails/export', [WebAuditTrailController::class, 'export'])->name('audit-trails.export');
        Route::get('/audit-trails/{auditTrail}', [WebAuditTrailController::class, 'show'])->name('audit-trails.show');
    });

    // Settings
    Route::middleware(['permission:manage-company-profile'])->group(function () {
        Route::get('/settings', [WebSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/company-profile', [WebSettingController::class, 'updateCompanyProfile'])->name('settings.update-company-profile');
        Route::post('/settings/update-profile', [WebSettingController::class, 'updateProfile'])->name('settings.update-profile');
        Route::post('/settings/backup', [WebSettingController::class, 'createBackup'])->name('settings.create-backup');
        Route::get('/settings/backup/{backupHistory}/download', [WebSettingController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/{backupHistory}/restore', [WebSettingController::class, 'restoreBackup'])->name('settings.backup.restore');
        Route::post('/settings/roles', [WebSettingController::class, 'createRole'])->name('settings.create-role');
        Route::put('/settings/roles/{role}', [WebSettingController::class, 'updateRole'])->name('settings.update-role');
        Route::post('/settings/roles/{role}/permissions', [WebSettingController::class, 'updateRolePermissions'])->name('settings.update-role-permissions');
        Route::delete('/settings/roles/{role}', [WebSettingController::class, 'deleteRole'])->name('settings.delete-role');
    });

    // Database Backup (for dashboard)
    Route::middleware(['permission:manage-company-profile'])->group(function () {
        Route::get('/backup/database', [WebSettingController::class, 'createBackup'])->name('backup.database');
    });
});
