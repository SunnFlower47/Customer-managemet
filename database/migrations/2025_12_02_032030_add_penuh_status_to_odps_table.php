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
        // Add 'penuh' to status enum
        \DB::statement("ALTER TABLE odps MODIFY COLUMN status ENUM('aktif', 'nonaktif', 'penuh') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        \DB::statement("ALTER TABLE odps MODIFY COLUMN status ENUM('aktif', 'nonaktif') DEFAULT 'aktif'");
    }
};
