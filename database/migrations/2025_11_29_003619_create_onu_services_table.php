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
        Schema::create('onu_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onu_id')->constrained('onus')->onDelete('cascade');
            $table->integer('service_id')->default(1); // Service 1-4 (multi-service support)
            $table->enum('wan_mode', ['pppoe', 'static', 'dhcp', 'bridge'])->default('pppoe');
            $table->string('pppoe_username')->nullable();
            $table->string('pppoe_password')->nullable();
            $table->string('static_ip')->nullable();
            $table->string('static_gateway')->nullable();
            $table->string('static_subnet')->nullable();
            $table->string('static_dns1')->nullable();
            $table->string('static_dns2')->nullable();
            $table->integer('vlan_id')->nullable();
            $table->integer('vlan_priority')->default(0);
            $table->boolean('vlan_tagged')->default(false);
            $table->foreignId('speed_profile_id')->nullable()->constrained('speed_profiles')->onDelete('set null');
            $table->integer('download_speed')->nullable(); // Kbps
            $table->integer('upload_speed')->nullable(); // Kbps
            $table->boolean('veip_enabled')->default(false);
            $table->string('veip_ip')->nullable();
            $table->boolean('tr069_enabled')->default(false);
            $table->string('tr069_url')->nullable();
            $table->json('lan_port_config')->nullable(); // Config untuk port LAN
            $table->json('wifi_config')->nullable(); // Config untuk WiFi
            $table->json('remote_access_rules')->nullable(); // Rules untuk remote access
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('onu_id');
            $table->index('service_id');
            $table->index('wan_mode');
            $table->index('vlan_id');
            $table->index('speed_profile_id');
            $table->unique(['onu_id', 'service_id'], 'onu_service_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onu_services');
    }
};
