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
        Schema::table('company_profiles', function (Blueprint $table) {
            // Add timestamps if they don't exist
            if (!Schema::hasColumn('company_profiles', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('company_profiles', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            // Only drop if they were added by this migration
            if (Schema::hasColumn('company_profiles', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('company_profiles', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
