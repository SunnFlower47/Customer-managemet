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
        Schema::table('users', function (Blueprint $table) {
            // Make role flexible by changing from enum to varchar
            // This allows adding new roles without migration
            $table->string('role', 255)->default('penagih')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('role', ['admin', 'penagih'])->default('penagih')->change();
        });
    }
};
