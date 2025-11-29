<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pelanggan;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        Log::info('Customer auth middleware', [
            'url' => $request->url(),
            'method' => $request->method(),
            'has_token' => !empty($token),
            'token_length' => $token ? strlen($token) : 0
        ]);

        if (!$token) {
            Log::warning('No token provided');
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        // Find the token in database
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            Log::warning('Token not found in database', [
                'token_prefix' => substr($token, 0, 10) . '...'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        // Get the customer from token
        $customer = $accessToken->tokenable;

        Log::info('Token validation', [
            'token_id' => $accessToken->id,
            'tokenable_type' => $accessToken->tokenable_type,
            'tokenable_id' => $accessToken->tokenable_id,
            'customer_found' => !empty($customer),
            'is_pelanggan' => $customer instanceof Pelanggan
        ]);

        if (!$customer || !($customer instanceof Pelanggan)) {
            Log::warning('Invalid customer from token', [
                'customer_type' => get_class($customer),
                'customer_id' => $customer->id ?? 'null'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid untuk customer'
            ], 401);
        }

        // Check if customer is active
        if ($customer->status !== 'aktif') {
            Log::warning('Customer not active', [
                'customer_id' => $customer->id,
                'status' => $customer->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Akun customer tidak aktif'
            ], 401);
        }

        Log::info('Customer authenticated successfully', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->nama,
            'status' => $customer->status
        ]);

        // Set the authenticated customer
        $request->setUserResolver(function () use ($customer) {
            return $customer;
        });

        return $next($request);
    }
}
