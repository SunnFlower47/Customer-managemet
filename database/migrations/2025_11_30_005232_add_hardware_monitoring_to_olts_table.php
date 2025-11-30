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
        Schema::table('olts', function (Blueprint $table) {
            $table->decimal('temperature', 5, 2)->nullable()->after('last_error'); // Temperature in Celsius
            $table->json('fan_speed')->nullable()->after('temperature'); // {fan1: speed, fan2: speed, ...}
            $table->enum('power_supply_status', ['normal', 'warning', 'critical', 'unknown'])->default('unknown')->after('fan_speed');
            $table->timestamp('last_temperature_check')->nullable()->after('power_supply_status');
            $table->timestamp('last_fan_check')->nullable()->after('last_temperature_check');
            $table->timestamp('last_power_check')->nullable()->after('last_fan_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('olts', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'fan_speed',
                'power_supply_status',
                'last_temperature_check',
                'last_fan_check',
                'last_power_check',
            ]);
        });
    }
};
