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
            $table->string('dana_phone')->nullable()->after('whatsapp');
            $table->string('mandiri_account')->nullable()->after('dana_phone');
            $table->string('mandiri_account_name')->nullable()->after('mandiri_account');
            $table->string('payment_whatsapp')->nullable()->after('mandiri_account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['dana_phone', 'mandiri_account', 'mandiri_account_name', 'payment_whatsapp']);
        });
    }
};
