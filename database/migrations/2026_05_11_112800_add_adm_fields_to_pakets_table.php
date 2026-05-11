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
        // 1. Tambah adm_persen ke company_profiles
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->decimal('adm_persen', 5, 2)->default(2.50)->after('uso_persen');
        });

        // 2. Tambah adm_nominal ke pakets
        Schema::table('pakets', function (Blueprint $table) {
            $table->decimal('adm_nominal', 12, 2)->nullable()->after('uso_nominal');
        });

        // 3. Hitung adm_nominal untuk data existing (2.5% dari harga_dasar)
        DB::statement('
            UPDATE pakets 
            SET adm_nominal = harga_dasar * 0.025
            WHERE harga_dasar IS NOT NULL AND harga_dasar > 0
        ');

        // 4. Update harga agar mencakup adm juga (harga_dasar + ppn + bhp + uso + adm)
        DB::statement('
            UPDATE pakets 
            SET harga = ROUND(harga_dasar + ppn_nominal + bhp_nominal + uso_nominal + adm_nominal)
            WHERE harga_dasar IS NOT NULL AND harga_dasar > 0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan harga ke sebelum adm ditambahkan
        DB::statement('
            UPDATE pakets 
            SET harga = ROUND(harga_dasar + ppn_nominal + bhp_nominal + uso_nominal)
            WHERE harga_dasar IS NOT NULL AND harga_dasar > 0
        ');

        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn('adm_nominal');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn('adm_persen');
        });
    }
};
