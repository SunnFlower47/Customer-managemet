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
        Schema::create('olt_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->onDelete('cascade');
            $table->enum('sync_type', ['full', 'incremental', 'status_only'])->default('full');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('processed_items')->default(0);
            $table->integer('new_onus')->default(0);
            $table->integer('updated_onus')->default(0);
            $table->integer('errors')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('details')->nullable(); // Additional sync details
            $table->timestamps();

            // Indexes
            $table->index('olt_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_sync_logs');
    }
};
