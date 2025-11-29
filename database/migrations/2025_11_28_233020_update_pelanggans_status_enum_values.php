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
        // Step 1: Add new enum values first (keep old values temporarily)
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'nonaktif', 'suspend', 'isolir', 'bayar double') DEFAULT 'aktif'");
        
        // Step 2: Update existing data
        DB::table('pelanggans')
            ->where('status', 'nonaktif')
            ->update(['status' => 'isolir']);
        
        DB::table('pelanggans')
            ->where('status', 'suspend')
            ->update(['status' => 'bayar double']);

        // Step 3: Remove old enum values
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'isolir', 'bayar double') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add old enum values back
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'isolir', 'bayar double', 'nonaktif', 'suspend') DEFAULT 'aktif'");
        
        // Step 2: Revert data back
        DB::table('pelanggans')
            ->where('status', 'isolir')
            ->update(['status' => 'nonaktif']);
        
        DB::table('pelanggans')
            ->where('status', 'bayar double')
            ->update(['status' => 'suspend']);

        // Step 3: Remove new enum values
        DB::statement("ALTER TABLE pelanggans MODIFY COLUMN status ENUM('aktif', 'nonaktif', 'suspend') DEFAULT 'aktif'");
    }
};
