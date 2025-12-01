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
            $table->foreignId('parent_odp_id')->nullable()->after('odc_id')->constrained('odps')->onDelete('set null');
            $table->index('parent_odp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['parent_odp_id']);
            $table->dropIndex(['parent_odp_id']);
            $table->dropColumn('parent_odp_id');
        });
    }
};
