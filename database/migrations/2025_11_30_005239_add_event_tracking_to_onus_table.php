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
        Schema::table('onus', function (Blueprint $table) {
            $table->boolean('unmapped_to_pelanggan')->default(false)->after('is_registered'); // Flag ONU belum dimapping
            $table->timestamp('last_event_at')->nullable()->after('last_synced_at'); // Last event timestamp
            $table->string('last_event_type')->nullable()->after('last_event_at'); // Last event type (los, dying_gasp, status_change, etc)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onus', function (Blueprint $table) {
            $table->dropColumn([
                'unmapped_to_pelanggan',
                'last_event_at',
                'last_event_type',
            ]);
        });
    }
};
