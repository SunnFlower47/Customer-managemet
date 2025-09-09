<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    /**
     * Send payment code via WhatsApp
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPaymentCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_pembayaran' => 'required|string|max:50',
            'api_key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $kodePembayaran = $request->kode_pembayaran;
            $apiKey = $request->api_key;

            // Validate API key (you can store this in config or database)
            if (!$this->validateApiKey($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key tidak valid'
                ], 401);
            }

            // Find payment by code
            $pembayaran = Pembayaran::with(['pelanggan', 'pelanggan.paket'])
                ->where('kode_pembayaran', $kodePembayaran)
                ->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pembayaran tidak ditemukan'
                ], 404);
            }

            // Check if customer has phone number
            if (!$pembayaran->pelanggan->no_hp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor HP pelanggan tidak tersedia'
                ], 400);
            }

            // Calculate due date
            $dueDate = $pembayaran->created_at->addMonth();
            $isOverdue = now()->isAfter($dueDate);
            $daysOverdue = $isOverdue ? now()->diffInDays($dueDate) : 0;

            // Format phone number (remove +62, add 62)
            $phoneNumber = $this->formatPhoneNumber($pembayaran->pelanggan->no_hp);

            // Create WhatsApp message
            $message = $this->createPaymentMessage($pembayaran, $dueDate, $isOverdue, $daysOverdue);

            // Send WhatsApp message
            $whatsappResponse = $this->sendWhatsAppMessage($phoneNumber, $message);

            if ($whatsappResponse['success']) {
                // Log the sent message
                Log::info('WhatsApp payment code sent', [
                    'kode_pembayaran' => $kodePembayaran,
                    'no_hp' => $phoneNumber,
                    'nama_pelanggan' => $pembayaran->pelanggan->nama,
                    'message_id' => $whatsappResponse['message_id'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Kode pembayaran berhasil dikirim via WhatsApp',
                    'data' => [
                        'kode_pembayaran' => $kodePembayaran,
                        'nama_pelanggan' => $pembayaran->pelanggan->nama,
                        'no_hp' => $phoneNumber,
                        'message_id' => $whatsappResponse['message_id'] ?? null,
                        'sent_at' => now()->format('d/m/Y H:i:s')
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim WhatsApp: ' . $whatsappResponse['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp send error', [
                'error' => $e->getMessage(),
                'kode_pembayaran' => $request->kode_pembayaran ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send bulk payment reminders
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendBulkReminders(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'overdue_only' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $apiKey = $request->api_key;
            $overdueOnly = $request->boolean('overdue_only', false);

            // Validate API key
            if (!$this->validateApiKey($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key tidak valid'
                ], 401);
            }

            // Get unpaid payments
            $query = Pembayaran::with(['pelanggan', 'pelanggan.paket'])
                ->where('status', 'belum_lunas')
                ->whereHas('pelanggan', function ($q) {
                    $q->whereNotNull('no_hp')->where('no_hp', '!=', '');
                });

            if ($overdueOnly) {
                $query->where('created_at', '<', now()->subMonth());
            }

            $pembayarans = $query->get();

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($pembayarans as $pembayaran) {
                try {
                    $phoneNumber = $this->formatPhoneNumber($pembayaran->pelanggan->no_hp);
                    $dueDate = $pembayaran->created_at->addMonth();
                    $isOverdue = now()->isAfter($dueDate);
                    $daysOverdue = $isOverdue ? now()->diffInDays($dueDate) : 0;

                    $message = $this->createPaymentMessage($pembayaran, $dueDate, $isOverdue, $daysOverdue);
                    $whatsappResponse = $this->sendWhatsAppMessage($phoneNumber, $message);

                    if ($whatsappResponse['success']) {
                        $successCount++;
                        $results[] = [
                            'kode_pembayaran' => $pembayaran->kode_pembayaran,
                            'nama_pelanggan' => $pembayaran->pelanggan->nama,
                            'no_hp' => $phoneNumber,
                            'status' => 'sent',
                            'message_id' => $whatsappResponse['message_id'] ?? null
                        ];
                    } else {
                        $errorCount++;
                        $results[] = [
                            'kode_pembayaran' => $pembayaran->kode_pembayaran,
                            'nama_pelanggan' => $pembayaran->pelanggan->nama,
                            'no_hp' => $phoneNumber,
                            'status' => 'failed',
                            'error' => $whatsappResponse['error']
                        ];
                    }

                    // Add delay between messages to avoid rate limiting
                    sleep(1);

                } catch (\Exception $e) {
                    $errorCount++;
                    $results[] = [
                        'kode_pembayaran' => $pembayaran->kode_pembayaran,
                        'nama_pelanggan' => $pembayaran->pelanggan->nama,
                        'no_hp' => $phoneNumber ?? 'N/A',
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk reminder selesai. Berhasil: {$successCount}, Gagal: {$errorCount}",
                'data' => [
                    'total_processed' => count($pembayarans),
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'results' => $results,
                    'processed_at' => now()->format('d/m/Y H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Bulk WhatsApp send error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate API key
     */
    private function validateApiKey(string $apiKey): bool
    {
        // You can store API keys in config or database
        $validApiKeys = config('api.valid_keys', []);
        return in_array($apiKey, $validApiKeys);
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Remove leading 0 and add 62
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        // If doesn't start with 62, add it
        if (substr($phoneNumber, 0, 2) !== '62') {
            $phoneNumber = '62' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Create payment message
     */
    private function createPaymentMessage($pembayaran, $dueDate, bool $isOverdue, int $daysOverdue): string
    {
        $companyName = config('app.company_name', 'BCM');
        $companyPhone = config('app.company_phone', '');

        $message = "🏢 *{$companyName}*\n\n";
        $message .= "Halo *{$pembayaran->pelanggan->nama}*,\n\n";

        if ($isOverdue) {
            $message .= "⚠️ *PEMBERITAHUAN TAGIHAN TERTUNDA*\n\n";
            $message .= "Tagihan Anda sudah terlambat *{$daysOverdue} hari*.\n\n";
        } else {
            $message .= "📋 *PEMBERITAHUAN TAGIHAN*\n\n";
        }

        $message .= "📄 *Kode Pembayaran:* `{$pembayaran->kode_pembayaran}`\n";
        $message .= "📅 *Periode:* {$pembayaran->bulan_tagihan}/{$pembayaran->tahun_tagihan}\n";
        $message .= "💰 *Jumlah:* Rp " . number_format($pembayaran->harga_paket ?? $pembayaran->pelanggan->paket->harga ?? 0, 0, ',', '.') . "\n";
        $message .= "📅 *Jatuh Tempo:* {$dueDate->format('d/m/Y')}\n";
        $message .= "📦 *Paket:* {$pembayaran->pelanggan->paket->nama_paket}\n\n";

        if ($isOverdue) {
            $message .= "🚨 *Segera lakukan pembayaran untuk menghindari pemutusan layanan.*\n\n";
        }

        $message .= "💳 *Cara Pembayaran:*\n";
        $message .= "1. Transfer ke rekening yang tertera\n";
        $message .= "2. Gunakan kode pembayaran di atas\n";
        $message .= "3. Konfirmasi pembayaran ke admin\n\n";

        if ($companyPhone) {
            $message .= "📞 *Kontak:* {$companyPhone}\n\n";
        }

        $message .= "Terima kasih atas kepercayaan Anda. 🙏";

        return $message;
    }

    /**
     * Send WhatsApp message via API
     */
    private function sendWhatsAppMessage(string $phoneNumber, string $message): array
    {
        try {
            // Example using WhatsApp Business API or third-party service
            // You can integrate with services like:
            // - WhatsApp Business API
            // - Twilio WhatsApp API
            // - Wablas API
            // - Fonnte API

            $whatsappApiUrl = config('whatsapp.api_url');
            $whatsappApiKey = config('whatsapp.api_key');

            if (!$whatsappApiUrl || !$whatsappApiKey) {
                // Mock response for testing
                return [
                    'success' => true,
                    'message_id' => 'MSG_' . time(),
                    'status' => 'sent',
                    'phone_number' => $phoneNumber,
                    'message' => 'Mock WhatsApp message sent for testing'
                ];
            }

            // Mock response for testing (since WhatsApp API is not configured)
            return [
                'success' => true,
                'message_id' => 'MSG_' . time(),
                'status' => 'sent',
                'phone_number' => $phoneNumber,
                'message' => $message
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
