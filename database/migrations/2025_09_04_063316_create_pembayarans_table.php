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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique();
            $table->unsignedBigInteger('pelanggan_id');
            $table->unsignedBigInteger('paket_id')->nullable();
            $table->string('nama_paket')->nullable();
            $table->decimal('harga_paket', 10, 2)->nullable();
            $table->integer('bulan_tagihan');
            $table->integer('tahun_tagihan');
            $table->decimal('jumlah', 10, 2);
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->unsignedBigInteger('penagih_id')->nullable();
            $table->string('nama_penagih')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('set null');
            $table->foreign('penagih_id')->references('id')->on('penagihs')->onDelete('set null');
            $table->unique(['pelanggan_id', 'bulan_tagihan', 'tahun_tagihan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
