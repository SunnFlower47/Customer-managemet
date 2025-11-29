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
        Schema::create('olts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_olt')->unique();
            $table->string('nama');
            $table->string('ip_address');
            $table->integer('port')->default(161); // SNMP port default, atau 23 untuk Telnet
            $table->string('snmp_community')->default('public');
            $table->string('vendor')->nullable(); // ZTE, Huawei, Fiberhome, dll
            $table->string('model')->nullable(); // C300, C320, MA5600T, dll
            $table->string('firmware_version')->nullable();
            $table->enum('connection_type', ['snmp', 'telnet', 'ssh', 'api'])->default('snmp');
            $table->string('api_endpoint')->nullable(); // Jika menggunakan API
            $table->string('username')->nullable(); // Untuk Telnet/SSH/API
            $table->text('password')->nullable(); // Encrypted
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('alamat')->nullable();
            $table->text('description')->nullable();
            $table->integer('total_ports')->default(0);
            $table->integer('ports_terpakai')->default(0);
            $table->integer('onu_terhubung')->default(0);
            $table->enum('status', ['online', 'offline', 'error'])->default('offline');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('is_active');
            $table->index('vendor');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olts');
    }
};
