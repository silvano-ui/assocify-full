<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    protected function success($data = [], $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($message = 'Error', $code = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
