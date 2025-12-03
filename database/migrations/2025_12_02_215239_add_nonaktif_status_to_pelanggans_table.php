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
        // Add 'nonaktif' to status enum
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'isolir', 'bayar double', 'nonaktif') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'nonaktif' from status enum
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'isolir', 'bayar double') DEFAULT 'aktif'");
    }
};
