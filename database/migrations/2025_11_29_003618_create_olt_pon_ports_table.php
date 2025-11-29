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
        Schema::create('olt_pon_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->onDelete('cascade');
            $table->integer('card')->default(1); // Card number
            $table->integer('port')->default(1); // Port number
            $table->string('port_name')->nullable(); // e.g., "gpon-olt_1/1/1"
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->integer('onu_count')->default(0);
            $table->decimal('rx_power', 8, 2)->nullable(); // RX power in dBm
            $table->decimal('tx_power', 8, 2)->nullable(); // TX power in dBm
            $table->text('description')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('olt_id');
            $table->index(['olt_id', 'card', 'port']);
            $table->index('status');
            $table->unique(['olt_id', 'card', 'port'], 'olt_port_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_pon_ports');
    }
};
