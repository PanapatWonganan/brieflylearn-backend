<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Return authentication error response
     * Used when user authentication fails in API controllers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authError(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
}
