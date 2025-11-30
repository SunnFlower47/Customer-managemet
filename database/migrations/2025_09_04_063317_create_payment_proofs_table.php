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
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_id'); // Foreign key ke pembayarans
            $table->unsignedBigInteger('pelanggan_id'); // Foreign key ke pelanggans
            $table->string('file_path'); // Path file bukti pembayaran
            $table->string('file_name'); // Nama file asli
            $table->string('file_type'); // image/jpeg, image/png, dll
            $table->bigInteger('file_size'); // Size file dalam bytes
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->unsignedBigInteger('verified_by')->nullable(); // Admin yang verifikasi
            $table->timestamp('verified_at')->nullable(); // Waktu verifikasi
            $table->enum('submission_method', ['website_upload', 'whatsapp_manual']); // Cara submit
            $table->string('whatsapp_message_id')->nullable(); // ID pesan WhatsApp (jika via WA)
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('cascade');
            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['pembayaran_id', 'status']);
            $table->index(['pelanggan_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};

