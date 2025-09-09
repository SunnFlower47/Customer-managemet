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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('nama_lengkap_perusahaan')->nullable();
            $table->string('inisial_perusahaan')->nullable();
            $table->text('alamat');
            $table->string('nomor_kontak');
            $table->string('whatsapp')->nullable();
            $table->string('email_support');
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('payment_code_prefix', 10)->default('PAY');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
