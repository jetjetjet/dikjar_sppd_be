<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Standard success response following the Envelope Pattern.
     */
    protected function successResponse(string $message, mixed $data = null, mixed $meta = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $code);
    }

    /**
     * Standard error response following the Envelope Pattern.
     */
    protected function errorResponse(string $message, mixed $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Validation error response (422).
     */
    protected function validationErrorResponse(string $message, array $errors): JsonResponse
    {
        return $this->errorResponse($message, $errors, 422);
    }
}
