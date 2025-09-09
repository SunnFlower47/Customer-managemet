<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for better performance on large datasets

        // Pelanggans table indexes
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->index(['status', 'penagih_id']);
            $table->index(['paket_id', 'status']);
            // Add tanggal_pembayaran column if it doesn't exist
            if (!Schema::hasColumn('pelanggans', 'tanggal_pembayaran')) {
                $table->integer('tanggal_pembayaran')->default(1)->after('tanggal_mulai');
            }
            $table->index('tanggal_pembayaran');
        });

        // Pembayarans table indexes
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->index(['status', 'penagih_id']);
            $table->index(['bulan_tagihan', 'tahun_tagihan']);
            $table->index(['pelanggan_id', 'bulan_tagihan', 'tahun_tagihan']);
            $table->index('tanggal_bayar');
        });

        // Penagihs table indexes
        Schema::table('penagihs', function (Blueprint $table) {
            $table->index(['aktif', 'user_id']);
        });

        // Pakets table indexes
        Schema::table('pakets', function (Blueprint $table) {
            $table->index('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropIndex(['status', 'penagih_id']);
            $table->dropIndex(['paket_id', 'status']);
            $table->dropIndex(['tanggal_pembayaran']);
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndex(['status', 'penagih_id']);
            $table->dropIndex(['bulan_tagihan', 'tahun_tagihan']);
            $table->dropIndex(['pelanggan_id', 'bulan_tagihan', 'tahun_tagihan']);
            $table->dropIndex(['tanggal_bayar']);
        });

        Schema::table('penagihs', function (Blueprint $table) {
            $table->dropIndex(['aktif', 'user_id']);
        });

        Schema::table('pakets', function (Blueprint $table) {
            $table->dropIndex(['aktif']);
        });
    }
};
