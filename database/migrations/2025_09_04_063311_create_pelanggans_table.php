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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pppoe')->unique();
            $table->text('alamat');
            $table->string('no_hp');
            $table->unsignedBigInteger('paket_id');
            $table->date('tanggal_mulai');
            $table->unsignedBigInteger('penagih_id');
            $table->enum('status', ['aktif', 'nonaktif', 'suspend'])->default('aktif');
            $table->timestamps();
            
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('cascade');
            $table->foreign('penagih_id')->references('id')->on('penagihs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
