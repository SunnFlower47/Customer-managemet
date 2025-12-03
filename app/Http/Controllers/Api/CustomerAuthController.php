<?php

namespace App\Http\Controllers\Api;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends BaseApiController
{
    /**
     * Maximum login attempts per minute
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Rate limit key for login
     */
    private function getLoginRateLimitKey(string $identifier): string
    {
        return 'login:' . $identifier;
    }
    /**
     * Customer login with phone number/PPPoE and password
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255', // Can be no_hp or pppoe
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            // Rate limiting based on username
            $rateLimitKey = $this->getLoginRateLimitKey($request->username);

            if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_LOGIN_ATTEMPTS)) {
                $retryAfter = RateLimiter::availableIn($rateLimitKey);
                return $this->errorResponse(
                    message: 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($retryAfter / 60) . ' menit.',
                    errorCode: 'TOO_MANY_LOGIN_ATTEMPTS',
                    statusCode: 429
                );
            }

            // Find customer by phone number or PPPoE
            // Allow login for customers with status 'aktif' or 'bayar double' (both are considered active)
            $customer = Pelanggan::where(function($query) use ($request) {
                $query->where('no_hp', $request->username)
                      ->orWhere('pppoe', $request->username);
            })
            ->whereIn('status', ['aktif', 'bayar double'])
            ->first();

            if (!$customer) {
                // Increment rate limiter on failed attempt (customer not found)
                RateLimiter::hit($rateLimitKey, 60);
                return $this->unauthorizedResponse('Username tidak ditemukan atau akun tidak aktif');
            }

            // Check if customer has password set
            if (!$customer->password) {
                // Increment rate limiter on failed attempt (no password set)
                RateLimiter::hit($rateLimitKey, 60);
                return $this->unauthorizedResponse('Password belum diatur. Silakan hubungi admin untuk mengatur password.');
            }

            // Verify password
            if (!Hash::check($request->password, $customer->password)) {
                // Increment rate limiter on failed attempt (wrong password)
                RateLimiter::hit($rateLimitKey, 60);
                return $this->unauthorizedResponse('Username atau password salah');
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($rateLimitKey);

            // Create token
            $token = $customer->createToken('customer-api-token', ['*'])->plainTextToken;

            // Update last login
            $customer->update([
                'last_login_at' => now()
            ]);

            return $this->successResponse([
                'customer' => $this->formatCustomerData($customer),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toISOString(),
            ], 'Login berhasil', 200);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Terjadi kesalahan saat login');
        }
    }

    /**
     * Format customer data for API response
     */
    private function formatCustomerData(Pelanggan $customer): array
    {
        return [
            'id' => $customer->id,
            'nama' => $customer->nama,
            'no_hp' => $customer->no_hp,
            'pppoe' => $customer->pppoe,
            'alamat' => $customer->alamat,
            'status' => $customer->status,
            'is_default_password' => $customer->is_default_password,
            'last_login_at' => $customer->last_login_at?->toISOString(),
            'paket' => $customer->paket ? [
                'id' => $customer->paket->id,
                'nama_paket' => $customer->paket->nama_paket,
                'harga' => $customer->paket->harga,
                'kecepatan' => $customer->paket->kecepatan,
            ] : null,
            'penagih' => $customer->penagih ? [
                'id' => $customer->penagih->id,
                'nama' => $customer->penagih->nama,
                'no_hp' => $customer->penagih->no_hp,
            ] : null,
        ];
    }

    /**
     * Customer logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            // Revoke current token
            $token = $request->bearerToken();
            if ($token) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken && $accessToken->tokenable_id === $customer->id) {
                    $accessToken->delete();
                }
            }

            // Optionally revoke all tokens for this customer
            // $customer->tokens()->delete();

            return $this->successResponse(null, 'Logout berhasil', 200);

        } catch (\Exception $e) {
            return $this->handleException($e, 'Terjadi kesalahan saat logout');
        }
    }

    /**
     * Get current customer information
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            if (!$customer) {
                return $this->unauthorizedResponse('Customer tidak ditemukan');
            }

            return $this->successResponse(
                $this->formatCustomerData($customer),
                'Data customer berhasil diambil',
                200
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'Terjadi kesalahan saat mengambil data customer');
        }
    }

    /**
     * Change customer password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
                'new_password_confirmation' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $customer = $request->user();

            if (!$customer) {
                return $this->unauthorizedResponse('Customer tidak ditemukan');
            }

            // Verify current password
            if (!Hash::check($request->current_password, $customer->password)) {
                return $this->unauthorizedResponse('Password lama salah');
            }

            // Check if new password is same as current password
            if (Hash::check($request->new_password, $customer->password)) {
                return $this->errorResponse(
                    message: 'Password baru harus berbeda dengan password lama',
                    errorCode: 'SAME_PASSWORD',
                    statusCode: 400
                );
            }

            // Update password
            $customer->update([
                'password' => Hash::make($request->new_password),
                'is_default_password' => false
            ]);

            return $this->successResponse(null, 'Password berhasil diubah', 200);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Terjadi kesalahan saat mengubah password');
        }
    }

    /**
     * Generate default password for customer (Admin only)
     */
    public function generateDefaultPassword(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            $user = $request->user();
            if (!$user || ($user->role ?? 'guest') !== 'admin') {
                return $this->forbiddenResponse('Hanya admin yang dapat mengakses endpoint ini');
            }

            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|integer|exists:pelanggans,id',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $customer = Pelanggan::findOrFail($request->customer_id);

            // Generate default password (universal default)
            $defaultPassword = '123456';

            $customer->update([
                'password' => Hash::make($defaultPassword),
                'is_default_password' => true
            ]);

            return $this->successResponse([
                'customer_id' => $customer->id,
                'nama' => $customer->nama,
                'no_hp' => $customer->no_hp,
                'default_password' => $defaultPassword,
                'message' => 'Password default adalah: ' . $defaultPassword
            ], 'Password default berhasil dibuat', 200);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Customer tidak ditemukan');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Terjadi kesalahan saat membuat password default');
        }
    }
}
