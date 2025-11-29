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
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id'); // Foreign key ke tickets
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key ke users (admin/penagih)
            $table->unsignedBigInteger('pelanggan_id')->nullable(); // Foreign key ke pelanggans
            $table->text('comment'); // Isi komentar
            $table->boolean('is_internal')->default(false); // Internal note untuk admin
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('set null');

            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index('is_internal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
