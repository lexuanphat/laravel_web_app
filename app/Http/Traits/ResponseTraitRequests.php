<?php

namespace App\Http\Traits;
use Illuminate\Http\JsonResponse;
trait ResponseTraitRequests {
    public function successResponse($data = [], $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message
        ]);
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