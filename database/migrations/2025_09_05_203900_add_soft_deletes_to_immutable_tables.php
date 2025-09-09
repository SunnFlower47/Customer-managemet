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
        // Add soft deletes to pembayarans table
        if (!Schema::hasColumn('pembayarans', 'deleted_at')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to pengeluarans table
        if (!Schema::hasColumn('pengeluarans', 'deleted_at')) {
            Schema::table('pengeluarans', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to audit_trails table
        if (!Schema::hasColumn('audit_trails', 'deleted_at')) {
            Schema::table('audit_trails', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
