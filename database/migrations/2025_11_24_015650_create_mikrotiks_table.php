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
        Schema::create('mikrotiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama router (misal: "Router Utama", "Router Cabang A")
            $table->string('ip_address');
            $table->integer('port')->default(8728); // API port default
            $table->string('username');
            $table->text('password'); // Encrypted
            $table->enum('routeros_version', ['v6', 'v7', 'v7.1+'])->default('v7');
            $table->string('location')->nullable(); // Lokasi fisik
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->string('connection_status')->default('unknown'); // online, offline, error
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('connection_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotiks');
    }
};
