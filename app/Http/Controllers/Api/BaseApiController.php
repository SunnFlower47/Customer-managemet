<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

abstract class BaseApiController extends Controller
{
    /**
     * Return a success JSON response
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Operation successful',
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(
        string $message = 'An error occurred',
        mixed $errors = null,
        ?string $errorCode = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a validation error response
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return $this->errorResponse(
            message: 'Validation failed',
            errors: $exception->errors(),
            errorCode: 'VALIDATION_ERROR',
            statusCode: 422
        );
    }

    /**
     * Return an unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse(
            message: $message,
            errorCode: 'UNAUTHORIZED',
            statusCode: 401
        );
    }

    /**
     * Return a forbidden response
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse(
            message: $message,
            errorCode: 'FORBIDDEN',
            statusCode: 403
        );
    }

    /**
     * Return a not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse(
            message: $message,
            errorCode: 'NOT_FOUND',
            statusCode: 404
        );
    }

    /**
     * Return an internal server error response
     */
    protected function serverErrorResponse(\Exception $exception, string $message = 'Internal server error'): JsonResponse
    {
        // Log error for debugging
        Log::error('API Error: ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => 'INTERNAL_SERVER_ERROR',
        ];

        // Only include error details in debug mode
        if (config('app.debug')) {
            $response['error'] = $exception->getMessage();
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
        }

        return response()->json($response, 500);
    }

    /**
     * Handle exception and return appropriate response
     */
    protected function handleException(\Exception $exception, string $defaultMessage = 'An error occurred'): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return $this->validationErrorResponse($exception);
        }

        return $this->serverErrorResponse($exception, $defaultMessage);
    }
}

