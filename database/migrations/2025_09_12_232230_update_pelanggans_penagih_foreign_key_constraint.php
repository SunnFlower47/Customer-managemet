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
        Schema::table('pelanggans', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['penagih_id']);

            // Make penagih_id nullable to support SET NULL
            $table->unsignedBigInteger('penagih_id')->nullable()->change();

            // Add the new foreign key constraint with SET NULL
            $table->foreign('penagih_id')->references('id')->on('penagihs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['penagih_id']);

            // Make penagih_id not nullable again
            $table->unsignedBigInteger('penagih_id')->nullable(false)->change();

            // Restore the original foreign key constraint with CASCADE
            $table->foreign('penagih_id')->references('id')->on('penagihs')->onDelete('cascade');
        });
    }
};
