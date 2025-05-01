<?php

namespace App\Http\Traits;
use Illuminate\Http\JsonResponse;
trait ResponseTraitRequests {
    public function successResponse($data = [], $message = '', $add_data = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
            ...$add_data,
        ];

        return response()->json($response);
    }

    public function errorResponse($message = '', $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => [],
            'message' => $message
        ], $status);
    }
}