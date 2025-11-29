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
        Schema::create('vlan_database', function (Blueprint $table) {
            $table->id();
            $table->integer('vlan_id')->unique();
            $table->string('nama');
            $table->text('description')->nullable();
            $table->string('purpose')->nullable(); // Internet, IPTV, VoIP, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('vlan_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vlan_database');
    }
};
