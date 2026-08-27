<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes on pembayarans to speed up common filter combinations:
     *   - paket_id alone
     *   - paket_id + status        (e.g. paket 100k & lunas)
     *   - paket_id + bulan + tahun (e.g. paket 100k, Juli 2026)
     *   - status + bulan + tahun   (already partly covered, add composite)
     */
    public function up(): void
    {
        $indexExists = fn($table, $name) => !empty(
            DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name])
        );

        Schema::table('pembayarans', function (Blueprint $table) use ($indexExists) {
            // Index on paket_id alone
            if (!$indexExists('pembayarans', 'pembayarans_paket_id_index')) {
                $table->index('paket_id', 'pembayarans_paket_id_index');
            }

            // Composite: paket_id + status  (filter paket lalu status)
            if (!$indexExists('pembayarans', 'pembayarans_paket_status_index')) {
                $table->index(['paket_id', 'status'], 'pembayarans_paket_status_index');
            }

            // Composite: paket_id + bulan + tahun  (filter paket lalu periode)
            if (!$indexExists('pembayarans', 'pembayarans_paket_bulan_tahun_index')) {
                $table->index(['paket_id', 'bulan_tagihan', 'tahun_tagihan'], 'pembayarans_paket_bulan_tahun_index');
            }

            // Composite: status + bulan + tahun  (common: semua paket, bulan Juli, lunas)
            if (!$indexExists('pembayarans', 'pembayarans_status_bulan_tahun_index')) {
                $table->index(['status', 'bulan_tagihan', 'tahun_tagihan'], 'pembayarans_status_bulan_tahun_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndexIfExists('pembayarans_paket_id_index');
            $table->dropIndexIfExists('pembayarans_paket_status_index');
            $table->dropIndexIfExists('pembayarans_paket_bulan_tahun_index');
            $table->dropIndexIfExists('pembayarans_status_bulan_tahun_index');
        });
    }
};
