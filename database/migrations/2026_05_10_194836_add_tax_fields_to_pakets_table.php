<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update company_profiles
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->decimal('ppn_persen', 5, 2)->default(11.00)->after('payment_code_prefix');
            $table->decimal('bhp_persen', 5, 2)->default(0.50)->after('ppn_persen');
            $table->decimal('uso_persen', 5, 2)->default(1.25)->after('bhp_persen');
        });

        // 2. Update pelanggans
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('nama');
        });

        // 3. Update pakets
        Schema::table('pakets', function (Blueprint $table) {
            $table->decimal('harga_dasar', 12, 2)->nullable()->after('nama_paket');
            $table->decimal('ppn_nominal', 12, 2)->nullable()->after('harga_dasar');
            $table->decimal('bhp_nominal', 12, 2)->nullable()->after('ppn_nominal');
            $table->decimal('uso_nominal', 12, 2)->nullable()->after('bhp_nominal');
        });

        // 4. Update data existing (Reverse Calculate)
        // Harga saat ini adalah Total (100% + 11% + 0.5% + 1.25% = 112.75%)
        // Harga Dasar = Harga / 1.1275
        DB::statement('
            UPDATE pakets 
            SET 
                harga_dasar = harga / 1.1275,
                ppn_nominal = (harga / 1.1275) * 0.11,
                bhp_nominal = (harga / 1.1275) * 0.005,
                uso_nominal = (harga / 1.1275) * 0.0125
            WHERE harga IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn(['harga_dasar', 'ppn_nominal', 'bhp_nominal', 'uso_nominal']);
        });

        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('nik');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['ppn_persen', 'bhp_persen', 'uso_persen']);
        });
    }
};
