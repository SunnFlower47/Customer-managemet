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
        Schema::create('speed_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('description')->nullable();
            $table->integer('download_speed'); // Kbps
            $table->integer('upload_speed'); // Kbps
            $table->string('profile_name')->nullable(); // Nama profile di OLT
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speed_profiles');
    }
};
