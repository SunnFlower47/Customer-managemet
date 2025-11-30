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
        Schema::create('onu_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onu_id')->constrained('onus')->onDelete('cascade');
            $table->enum('event_type', [
                'los',
                'dying_gasp',
                'status_change',
                'rx_power_change',
                'tx_power_change',
                'online',
                'offline',
                'reboot',
                'reset',
                'disabled',
                'enabled',
                'config_changed',
                'vlan_changed',
                'serial_changed',
            ])->index();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info')->index();
            $table->string('old_value')->nullable(); // Old status/value
            $table->string('new_value')->nullable(); // New status/value
            $table->json('event_data')->nullable(); // Additional event data
            $table->text('message')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User who triggered (if manual)
            $table->timestamp('resolved_at')->nullable(); // When event was resolved
            $table->timestamps();

            // Indexes
            $table->index(['onu_id', 'event_type']);
            $table->index(['onu_id', 'severity']);
            $table->index('created_at');
            $table->index(['event_type', 'severity']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onu_events');
    }
};
