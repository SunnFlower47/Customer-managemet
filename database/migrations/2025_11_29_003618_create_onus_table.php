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
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->onDelete('cascade');
            $table->foreignId('olt_pon_port_id')->nullable()->constrained('olt_pon_ports')->onDelete('set null');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('set null');
            $table->foreignId('odp_id')->nullable()->constrained('odps')->onDelete('set null');
            $table->string('serial_number')->unique();
            $table->string('mac_address')->nullable();
            $table->string('nama')->nullable();
            $table->text('description')->nullable();
            $table->string('ont_type')->nullable(); // Tipe ONT/ONU
            $table->string('vendor')->nullable();
            $table->string('model')->nullable();
            $table->enum('status', ['online', 'offline', 'dying_gasp', 'los', 'unknown'])->default('unknown');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_registered')->default(false);
            $table->integer('card')->nullable();
            $table->integer('port')->nullable();
            $table->integer('onu_id')->nullable(); // ONU ID di OLT
            $table->decimal('rx_power', 8, 2)->nullable(); // RX power dari modem (dBm)
            $table->decimal('olt_rx_power', 8, 2)->nullable(); // RX power dari sisi OLT (dBm)
            $table->decimal('tx_power', 8, 2)->nullable(); // TX power (dBm)
            $table->string('ip_address')->nullable();
            $table->string('gateway')->nullable();
            $table->string('subnet_mask')->nullable();
            $table->integer('uptime_seconds')->default(0); // Durasi online dalam detik
            $table->timestamp('last_online_at')->nullable();
            $table->timestamp('last_offline_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('olt_config')->nullable(); // JSON config dari OLT
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('olt_id');
            $table->index('olt_pon_port_id');
            $table->index('pelanggan_id');
            $table->index('odp_id');
            $table->index('status');
            $table->index('is_active');
            $table->index('is_registered');
            $table->index(['olt_id', 'card', 'port']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onus');
    }
};
