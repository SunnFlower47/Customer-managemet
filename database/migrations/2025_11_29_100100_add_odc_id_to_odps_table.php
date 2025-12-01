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
        Schema::table('odps', function (Blueprint $table) {
            $table->foreignId('odc_id')->nullable()->after('olt_id')->constrained('odcs')->onDelete('set null');
            $table->index('odc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['odc_id']);
            $table->dropIndex(['odc_id']);
            $table->dropColumn('odc_id');
        });
    }
};


