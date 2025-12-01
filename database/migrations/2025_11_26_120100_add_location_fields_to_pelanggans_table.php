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
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->unsignedBigInteger('odp_id')->nullable()->after('longitude');

            $table->foreign('odp_id')->references('id')->on('odps')->onDelete('set null');
            $table->index(['latitude', 'longitude']);
            $table->index('odp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropForeign(['odp_id']);
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['odp_id']);
            $table->dropColumn(['latitude', 'longitude', 'odp_id']);
        });
    }
};


