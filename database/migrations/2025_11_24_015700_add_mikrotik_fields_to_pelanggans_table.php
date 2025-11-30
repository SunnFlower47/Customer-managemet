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
            // Use 'status' column which definitely exists, or add at the end if status doesn't exist
            // Note: odp_id is added later in migration 2025_11_26_120100, so we can't use it here
            $afterColumn = 'status'; // This column exists in the base pelanggans table

            $table->unsignedBigInteger('mikrotik_id')->nullable()->after($afterColumn);
            $table->boolean('exists_in_mikrotik')->nullable()->default(false)->after('mikrotik_id');
            $table->timestamp('mikrotik_last_checked')->nullable()->after('exists_in_mikrotik');
            $table->string('mikrotik_router_name')->nullable()->after('mikrotik_last_checked');
            $table->string('mikrotik_status')->nullable()->after('mikrotik_router_name'); // active/disabled
            $table->string('mikrotik_ip')->nullable()->after('mikrotik_status');
            $table->string('mikrotik_profile')->nullable()->after('mikrotik_ip');

            // Foreign key
            $table->foreign('mikrotik_id')->references('id')->on('mikrotiks')->onDelete('set null');

            // Indexes
            $table->index('exists_in_mikrotik');
            $table->index('mikrotik_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropForeign(['mikrotik_id']);
            $table->dropIndex(['exists_in_mikrotik']);
            $table->dropIndex(['mikrotik_id']);
            $table->dropColumn([
                'mikrotik_id',
                'exists_in_mikrotik',
                'mikrotik_last_checked',
                'mikrotik_router_name',
                'mikrotik_status',
                'mikrotik_ip',
                'mikrotik_profile',
            ]);
        });
    }
};
