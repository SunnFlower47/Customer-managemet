<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class BaseController extends Controller
{
    /**
     * Handle API responses consistently
     */
    protected function apiResponse($data = null, $message = 'Success', $status = 200, $errors = null)
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'timestamp' => now()->toISOString()
        ], $status);
    }

    /**
     * Handle web responses with flash messages
     */
    protected function webResponse($redirect, $type, $message, $data = [])
    {
        if ($redirect instanceof \Illuminate\Http\RedirectResponse) {
            $redirect = $redirect->with($type, $message);
        } else {
            $redirect = redirect($redirect)->with($type, $message);
        }

        // Add additional data to session
        foreach ($data as $key => $value) {
            $redirect = $redirect->with($key, $value);
        }

        return $redirect;
    }

    /**
     * Validate request with custom error handling
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ];
        }

        return ['success' => true];
    }

    /**
     * Handle exceptions with logging
     */
    protected function handleException(Exception $e, $context = 'General', $userMessage = 'Terjadi kesalahan sistem')
    {
        // Log the exception
        Log::error("{$context} Error: " . $e->getMessage(), [
            'exception' => $e,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return appropriate response based on request type
        if (request()->expectsJson()) {
            return $this->apiResponse(
                null,
                $userMessage,
                500,
                config('app.debug') ? $e->getMessage() : null
            );
        }

        return back()->with('error', $userMessage);
    }

    /**
     * Log user actions for audit trail
     */
    protected function logUserAction($action, $model = null, $modelId = null, $details = [])
    {
        try {
            Log::info("User Action: {$action}", [
                'user_id' => Auth::id(),
                'user_name' => optional(Auth::user())->name ?? 'Unknown',
                'model' => $model,
                'model_id' => $modelId,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            // Don't let logging errors break the main flow
            Log::error('Failed to log user action: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to a named route while preserving current query parameters
     * and attaching a flash message.
     */
    protected function redirectToRouteWithParams(string $routeName, Request $request, string $message, $type = 'success', array $routeParams = [])
    {
        // Allow omitting the flash type when wanting to pass route parameters directly
        if (is_array($type) && empty($routeParams)) {
            $routeParams = $type;
            $type = 'success';
        }

        // Preserve any existing query string parameters (filters, pagination, etc.)
        $queryParams = $request->query();
        $params = array_merge($routeParams, $queryParams);

        return redirect()->route($routeName, $params)->with($type, $message);
    }

    /**
     * Check if user has permission
     */
    protected function checkPermission($permission)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $hasPermission = $user && method_exists($user, 'can') ? $user->can($permission) : false;
        if (!$hasPermission) {
            if (request()->expectsJson()) {
                return $this->apiResponse(
                    null,
                    'Anda tidak memiliki izin untuk melakukan aksi ini',
                    403
                );
            }

            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        return null;
    }

    /**
     * Format currency consistently
     */
    protected function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format date consistently
     */
    protected function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) return '-';

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (Exception $e) {
            return '-';
        }
    }

    /**
     * Get pagination data
     */
    protected function getPaginationData($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }
}
