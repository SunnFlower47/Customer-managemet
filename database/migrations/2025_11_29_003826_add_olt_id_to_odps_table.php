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
            $table->foreignId('olt_id')->nullable()->after('id')->constrained('olts')->onDelete('set null');
            $table->index('olt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['olt_id']);
            $table->dropIndex(['olt_id']);
            $table->dropColumn('olt_id');
        });
    }
};
