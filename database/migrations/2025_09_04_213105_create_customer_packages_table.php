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
        Schema::create('customer_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('package_id');
            $table->date('start_date');
            $table->date('end_date')->nullable(); // NULL jika masih aktif
            $table->decimal('price', 10, 2); // Harga saat paket aktif
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('pakets')->onDelete('cascade');

            $table->index(['customer_id', 'start_date']);
            $table->index(['customer_id', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_packages');
    }
};
