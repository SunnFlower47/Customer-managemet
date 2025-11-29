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
            $table->dropForeign(['paket_id']);

            // Make paket_id nullable to support SET NULL
            $table->unsignedBigInteger('paket_id')->nullable()->change();

            // Add the new foreign key constraint with SET NULL
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['paket_id']);

            // Make paket_id not nullable again
            $table->unsignedBigInteger('paket_id')->nullable(false)->change();

            // Restore the original foreign key constraint with CASCADE
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('cascade');
        });
    }
};
