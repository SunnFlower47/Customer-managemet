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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('nama_pengeluaran');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_pengeluaran');
            $table->string('metode_pembayaran')->default('tunai'); // tunai, transfer, kartu
            $table->string('status')->default('terkonfirmasi'); // terkonfirmasi, pending, dibatalkan
            $table->string('bukti_pembayaran')->nullable(); // path to file
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['tanggal_pengeluaran', 'kategori']);
            $table->index(['status', 'tanggal_pengeluaran']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};