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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ticket', 20)->unique(); // TKT20250914001
            $table->unsignedBigInteger('pelanggan_id'); // Foreign key ke pelanggans
            $table->string('judul', 255); // "WiFi sering putus"
            $table->text('deskripsi'); // Detail masalah
            $table->enum('kategori', ['technical', 'billing', 'service', 'other'])->default('technical');
            $table->enum('prioritas', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable(); // Foreign key ke users (penagih/technician)
            $table->timestamp('resolved_at')->nullable();
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('customer_feedback')->nullable(); // Feedback dari customer
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['pelanggan_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('status');
            $table->index('kategori');
            $table->index('prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
