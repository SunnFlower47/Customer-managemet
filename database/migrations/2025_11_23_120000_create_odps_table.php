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
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('kode_odp')->unique();
            $table->string('nama');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('alamat')->nullable();
            $table->integer('kapasitas')->default(0);
            $table->integer('port_terpakai')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};


