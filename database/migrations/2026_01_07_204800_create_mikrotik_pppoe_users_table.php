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
        Schema::create('mikrotik_pppoe_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mikrotik_id')->constrained('mikrotiks')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->onDelete('set null');
            
            $table->string('username')->index();
            $table->string('password')->nullable();
            $table->string('service')->nullable(); // pppoe
            $table->string('profile')->nullable();
            $table->string('local_address')->nullable();
            $table->string('remote_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('status')->default('enabled'); // enabled, disabled
            $table->boolean('is_active')->default(false); // connected status
            $table->timestamp('last_seen')->nullable();
            
            $table->timestamps();
            
            // Uniqueness: username per router
            $table->unique(['mikrotik_id', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_pppoe_users');
    }
};
