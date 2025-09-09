<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function to check if index exists
        $indexExists = function($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM $table WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        };

        // Add missing indexes for foreign keys
        if (!$indexExists('model_has_permissions', 'model_has_permissions_permission_id_index')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->index('permission_id');
            });
        }

        if (!$indexExists('model_has_roles', 'model_has_roles_role_id_index')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->index('role_id');
            });
        }

        if (!$indexExists('role_has_permissions', 'role_has_permissions_permission_id_index')) {
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $table->index('permission_id');
            });
        }

        if (!$indexExists('role_has_permissions', 'role_has_permissions_role_id_index')) {
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $table->index('role_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_permission_id_index');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_role_id_index');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropIndex('role_has_permissions_permission_id_index');
            $table->dropIndex('role_has_permissions_role_id_index');
        });
    }
};
