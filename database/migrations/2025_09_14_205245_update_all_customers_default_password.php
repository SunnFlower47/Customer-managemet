<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Pelanggan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update password default untuk semua customer yang sudah ada
        Pelanggan::where('status', 'aktif')->update([
            'password' => Hash::make('123456'), // Password default sama untuk semua
            'is_default_password' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak bisa di-reverse karena password sudah di-hash
        // Admin harus reset password manual jika diperlukan
    }
};
