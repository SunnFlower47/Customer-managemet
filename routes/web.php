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
use App\Http\Controllers\Web\AdminTicketController;
use App\Http\Controllers\Web\AdminPaymentProofController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\OdpController;
use App\Http\Controllers\Web\MappingController;
use App\Http\Controllers\Web\MikrotikController;

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
    return view('welcome');
})->name('welcome');

// Customer Portal Routes (Public - Serve React App)
Route::get('/customer-portal', function () {
    $filePath = public_path('../customer-portal/build/index.html');
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    return response()->json(['error' => 'Customer portal not found'], 404);
})->name('customer-portal.index');

// Customer Portal Static Files
Route::get('/customer-portal/static/{path}', function ($path) {
    $filePath = public_path("../customer-portal/build/static/{$path}");
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    return response()->json(['error' => 'Static file not found'], 404);
})->where('path', '.*');

// Customer Portal SPA Routes (catch all)
Route::get('/customer-portal/*', function () {
    $filePath = public_path('../customer-portal/build/index.html');
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    return response()->json(['error' => 'Customer portal not found'], 404);
})->name('customer-portal.spa');

Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/clear-cache', [WebDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
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
        Route::get('/pelanggans/suggestions', [WebPelangganController::class, 'suggest'])->name('pelanggans.suggestions');
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
        Route::post('/pelanggans/bulk-delete', [WebPelangganController::class, 'bulkDelete'])->name('pelanggans.bulk-delete');
    });

    Route::middleware(['permission:edit-pelanggan'])->group(function () {
        Route::post('/pelanggans/bulk-update-status', [WebPelangganController::class, 'bulkUpdateStatus'])->name('pelanggans.bulk-update-status');
    });

    Route::middleware(['permission:export-pelanggan'])->group(function () {
        Route::get('/pelanggans/export/pdf', [WebPelangganController::class, 'exportPdf'])->name('pelanggans.export.pdf');
    });

    // Pembayarans
    Route::middleware(['permission:view-pembayaran'])->group(function () {
        Route::get('/pembayarans', [WebPembayaranController::class, 'index'])->name('pembayarans.index');
        Route::get('/pembayarans/suggestions', [WebPembayaranController::class, 'suggest'])->name('pembayarans.suggestions');
        Route::get('/pembayarans/{pembayaran}', [WebPembayaranController::class, 'show'])->name('pembayarans.show');
    });

    // Pembayaran create/store routes removed - payments are generated automatically

    Route::middleware(['permission:edit-pembayaran'])->group(function () {
        Route::get('/pembayarans/{pembayaran}/edit', [WebPembayaranController::class, 'edit'])->name('pembayarans.edit');
        Route::put('/pembayarans/{pembayaran}', [WebPembayaranController::class, 'update'])->name('pembayarans.update');
        Route::patch('/pembayarans/{pembayaran}/status', [WebPembayaranController::class, 'updateStatus'])->name('pembayarans.update-status');
        Route::patch('/pembayarans/{pembayaran}/mark-paid', [WebPembayaranController::class, 'markPaid'])->name('pembayarans.mark-paid');
        Route::post('/pembayarans/bulk-update-status', [WebPembayaranController::class, 'bulkUpdateStatus'])->name('pembayarans.bulk-update-status');
        Route::post('/pembayarans/bulk-mark-paid', [WebPembayaranController::class, 'bulkMarkPaid'])->name('pembayarans.bulk-mark-paid');
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
        Route::get('/settings/backup/download/{filename}', [WebSettingController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/roles', [WebSettingController::class, 'createRole'])->name('settings.create-role');
        Route::put('/settings/roles/{role}', [WebSettingController::class, 'updateRole'])->name('settings.update-role');
        Route::post('/settings/roles/{role}/permissions', [WebSettingController::class, 'updateRolePermissions'])->name('settings.update-role-permissions');
        Route::delete('/settings/roles/{role}', [WebSettingController::class, 'deleteRole'])->name('settings.delete-role');
    });

    // Database Backup (for dashboard)
    Route::middleware(['permission:manage-company-profile'])->group(function () {
        Route::get('/backup/database', [WebSettingController::class, 'createBackup'])->name('backup.database');
    });

    // Admin Ticket Management
    Route::middleware(['permission:view-ticket'])->group(function () {
        Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/admin/tickets/statistics', [AdminTicketController::class, 'statistics'])->name('admin.tickets.statistics');
        Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
    });

    Route::middleware(['permission:edit-ticket'])->group(function () {
        Route::put('/admin/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('admin.tickets.update-status');
        Route::put('/admin/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('admin.tickets.assign');
        Route::post('/admin/tickets/{ticket}/comments', [AdminTicketController::class, 'addComment'])->name('admin.tickets.add-comment');
    });

    // Admin Payment Proof Management
    Route::middleware(['permission:view-payment-proof'])->group(function () {
        Route::get('/admin/payment-proofs', [AdminPaymentProofController::class, 'index'])->name('admin.payment-proofs.index');
        Route::get('/admin/payment-proofs/statistics', [AdminPaymentProofController::class, 'statistics'])->name('admin.payment-proofs.statistics');
        Route::get('/admin/payment-proofs/{paymentProof}', [AdminPaymentProofController::class, 'show'])->name('admin.payment-proofs.show');
        Route::get('/admin/payment-proofs/{paymentProof}/download', [AdminPaymentProofController::class, 'download'])->name('admin.payment-proofs.download');
    });

    Route::middleware(['permission:verify-payment-proof'])->group(function () {
        Route::put('/admin/payment-proofs/{paymentProof}/verify', [AdminPaymentProofController::class, 'verify'])->name('admin.payment-proofs.verify');
        Route::put('/admin/payment-proofs/{paymentProof}/reject', [AdminPaymentProofController::class, 'reject'])->name('admin.payment-proofs.reject');
    });

    // Customer Portal Management
    Route::middleware(['permission:view-customer-portal'])->group(function () {
        Route::get('/customer-portal', function () {
            return view('admin.dashboard');
        })->name('customer-portal.index');
    });

    // Mapping
    Route::middleware(['permission:view-mapping'])->group(function () {
        Route::get('/mapping', [MappingController::class, 'index'])->name('mapping.index');
        Route::get('/mapping/search-pelanggans', [MappingController::class, 'searchPelanggans'])->name('mapping.search-pelanggans');
    });

    // MikroTik Management
    Route::middleware(['permission:view-mikrotik'])->group(function () {
        Route::get('/mikrotiks', [MikrotikController::class, 'index'])->name('mikrotiks.index');
    });

    Route::middleware(['permission:manage-mikrotik'])->group(function () {
        Route::get('/mikrotiks/create', [MikrotikController::class, 'create'])->name('mikrotiks.create');
        Route::post('/mikrotiks', [MikrotikController::class, 'store'])->name('mikrotiks.store');
        Route::get('/mikrotiks/{mikrotik}/edit', [MikrotikController::class, 'edit'])->name('mikrotiks.edit');
        Route::put('/mikrotiks/{mikrotik}', [MikrotikController::class, 'update'])->name('mikrotiks.update');
        Route::delete('/mikrotiks/{mikrotik}', [MikrotikController::class, 'destroy'])->name('mikrotiks.destroy');
    });

    Route::middleware(['permission:view-mikrotik'])->group(function () {
        Route::get('/mikrotiks/{mikrotik}', [MikrotikController::class, 'show'])->name('mikrotiks.show');
        Route::post('/mikrotiks/{mikrotik}/test-connection', [MikrotikController::class, 'testConnection'])->name('mikrotiks.test-connection');
        Route::post('/mikrotiks/{mikrotik}/search-pppoe', [MikrotikController::class, 'searchPppoe'])->name('mikrotiks.search-pppoe');
    });

    // Mapping - Update pelanggan location (Admin only)
    Route::middleware(['permission:manage-mapping'])->group(function () {
        Route::put('/mapping/pelanggans/{pelanggan}/location', [MappingController::class, 'updatePelangganLocation'])->name('mapping.update-pelanggan-location');
    });

    // ODP Management (view)
    Route::middleware(['permission:view-odp'])->group(function () {
        Route::get('/odps', [OdpController::class, 'index'])->name('odps.index');
    });

    // ODP Management (Admin only - create/edit/delete)
    // IMPORTANT: Routes with specific paths (like /create, /edit) must come BEFORE parameterized routes (/{odp})
    Route::middleware(['permission:manage-odp'])->group(function () {
        Route::get('/odps/create', [OdpController::class, 'create'])->name('odps.create');
        Route::post('/odps', [OdpController::class, 'store'])->name('odps.store');
        Route::get('/odps/{odp}/edit', [OdpController::class, 'edit'])->name('odps.edit');
        Route::put('/odps/{odp}', [OdpController::class, 'update'])->name('odps.update');
        Route::delete('/odps/{odp}', [OdpController::class, 'destroy'])->name('odps.destroy');
    });

    Route::middleware(['permission:view-odp'])->group(function () {
        Route::get('/odps/{odp}', [OdpController::class, 'show'])->name('odps.show');
    });

    // OLT Monitoring Dashboard
    Route::middleware(['permission:view-olt'])->group(function () {
        Route::get('/olt-monitoring/dashboard', [\App\Http\Controllers\Web\OltDashboardController::class, 'index'])->name('olt-monitoring.dashboard');
    });

    // OLT Management
    Route::middleware(['permission:view-olt'])->group(function () {
        Route::get('/olts', [\App\Http\Controllers\Web\OltController::class, 'index'])->name('olts.index');
    });

    Route::middleware(['permission:manage-olt'])->group(function () {
        Route::get('/olts/create', [\App\Http\Controllers\Web\OltController::class, 'create'])->name('olts.create');
        Route::post('/olts', [\App\Http\Controllers\Web\OltController::class, 'store'])->name('olts.store');
        Route::get('/olts/{olt}/edit', [\App\Http\Controllers\Web\OltController::class, 'edit'])->name('olts.edit');
        Route::put('/olts/{olt}', [\App\Http\Controllers\Web\OltController::class, 'update'])->name('olts.update');
        Route::delete('/olts/{olt}', [\App\Http\Controllers\Web\OltController::class, 'destroy'])->name('olts.destroy');
        Route::post('/olts/monitor-all', [\App\Http\Controllers\Web\OltController::class, 'monitorAll'])->name('olts.monitor-all');
    });

    // OLT Sync (specific paths must come before parameterized routes)
    Route::middleware(['permission:sync-olt'])->group(function () {
        Route::post('/olts/{olt}/sync', [\App\Http\Controllers\Web\OltSyncController::class, 'sync'])->name('olts.sync');
        Route::get('/olts/sync/{syncLog}/progress', [\App\Http\Controllers\Web\OltSyncController::class, 'getProgress'])->name('olts.sync.progress');
    });

    Route::middleware(['permission:view-olt'])->group(function () {
        Route::get('/olts/{olt}', [\App\Http\Controllers\Web\OltController::class, 'show'])->name('olts.show');
        Route::post('/olts/{olt}/test-connection', [\App\Http\Controllers\Web\OltController::class, 'testConnection'])->name('olts.test-connection');
    });

    // ONU Management
    Route::middleware(['permission:view-onu'])->group(function () {
        Route::get('/onus', [\App\Http\Controllers\Web\OnuController::class, 'index'])->name('onus.index');
    });

    // ONU Register (place before parameterized /onus/{onu})
    Route::middleware(['permission:manage-onu'])->group(function () {
        Route::get('/onus/register', [\App\Http\Controllers\Web\OnuRegisterController::class, 'create'])->name('onus.register');
        Route::post('/onus/register', [\App\Http\Controllers\Web\OnuRegisterController::class, 'store'])->name('onus.register.store');
    });

    Route::middleware(['permission:view-onu'])->group(function () {
        Route::get('/onus/{onu}', [\App\Http\Controllers\Web\OnuController::class, 'show'])->name('onus.show');
    });

    Route::middleware(['permission:manage-onu'])->group(function () {
        Route::put('/onus/{onu}', [\App\Http\Controllers\Web\OnuController::class, 'update'])->name('onus.update');
        Route::delete('/onus/{onu}', [\App\Http\Controllers\Web\OnuController::class, 'destroy'])->name('onus.destroy');
    });

    Route::middleware(['permission:reboot-onu'])->group(function () {
        Route::post('/onus/{onu}/reboot', [\App\Http\Controllers\Web\OnuController::class, 'reboot'])->name('onus.reboot');
        Route::post('/onus/{onu}/reset', [\App\Http\Controllers\Web\OnuController::class, 'reset'])->name('onus.reset');
        Route::post('/onus/{onu}/disable', [\App\Http\Controllers\Web\OnuController::class, 'disable'])->name('onus.disable');
        Route::post('/onus/{onu}/enable', [\App\Http\Controllers\Web\OnuController::class, 'enable'])->name('onus.enable');
        Route::post('/onus/{onu}/clear-config', [\App\Http\Controllers\Web\OnuController::class, 'clearConfig'])->name('onus.clear-config');
        Route::post('/onus/{onu}/change-serial', [\App\Http\Controllers\Web\OnuController::class, 'changeSerialNumber'])->name('onus.change-serial');
    });

    // ONU Service Management
    Route::middleware(['permission:manage-onu'])->group(function () {
        Route::post('/onus/{onu}/services', [\App\Http\Controllers\Web\OnuServiceController::class, 'store'])->name('onus.services.store');
        Route::put('/onus/{onu}/services/{service}', [\App\Http\Controllers\Web\OnuServiceController::class, 'update'])->name('onus.services.update');
        Route::delete('/onus/{onu}/services/{service}', [\App\Http\Controllers\Web\OnuServiceController::class, 'destroy'])->name('onus.services.destroy');
        Route::put('/onus/{onu}/services/{service}/remote-access', [\App\Http\Controllers\Web\OnuServiceController::class, 'updateRemoteAccess'])->name('onus.services.remote-access');
    });

    // VLAN Database
    Route::middleware(['permission:view-vlan'])->group(function () {
        Route::get('/vlans', [\App\Http\Controllers\Web\VlanController::class, 'index'])->name('vlans.index');
    });

    Route::middleware(['permission:manage-vlan'])->group(function () {
        Route::post('/vlans', [\App\Http\Controllers\Web\VlanController::class, 'store'])->name('vlans.store');
        Route::put('/vlans/{vlan}', [\App\Http\Controllers\Web\VlanController::class, 'update'])->name('vlans.update');
        Route::delete('/vlans/{vlan}', [\App\Http\Controllers\Web\VlanController::class, 'destroy'])->name('vlans.destroy');
    });

    // Speed Profiles
    Route::middleware(['permission:view-speed-profile'])->group(function () {
        Route::get('/speed-profiles', [\App\Http\Controllers\Web\SpeedProfileController::class, 'index'])->name('speed-profiles.index');
    });

    Route::middleware(['permission:manage-speed-profile'])->group(function () {
        Route::post('/speed-profiles', [\App\Http\Controllers\Web\SpeedProfileController::class, 'store'])->name('speed-profiles.store');
        Route::put('/speed-profiles/{speedProfile}', [\App\Http\Controllers\Web\SpeedProfileController::class, 'update'])->name('speed-profiles.update');
        Route::delete('/speed-profiles/{speedProfile}', [\App\Http\Controllers\Web\SpeedProfileController::class, 'destroy'])->name('speed-profiles.destroy');
    });
});
