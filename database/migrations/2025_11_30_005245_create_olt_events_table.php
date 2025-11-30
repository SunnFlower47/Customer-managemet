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
        Schema::create('olt_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->onDelete('cascade');
            $table->enum('event_type', [
                'los',
                'dying_gasp',
                'temperature_high',
                'temperature_critical',
                'fan_failure',
                'fan_slow',
                'power_supply_failure',
                'power_supply_warning',
                'connection_lost',
                'connection_restored',
                'sync_started',
                'sync_completed',
                'sync_failed',
            ])->index();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info')->index();
            $table->json('event_data')->nullable(); // Additional event data
            $table->text('message')->nullable();
            $table->timestamp('resolved_at')->nullable(); // When event was resolved
            $table->timestamps();

            // Indexes
            $table->index(['olt_id', 'event_type']);
            $table->index(['olt_id', 'severity']);
            $table->index('created_at');
            $table->index(['event_type', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_events');
    }
};
