<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function success($data = [], $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return JsonResponse
     */
    public function error($message = 'Error', $code = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a paginated JSON response.
     *
     * @param mixed $query
     * @param int $perPage
     * @param string|null $resource
     * @return JsonResponse
     */
    public function paginate($query, $perPage = 15, $resource = null): JsonResponse
    {
        $paginator = $query->paginate($perPage);
        
        $items = $paginator->items();
        if ($resource && class_exists($resource)) {
            $items = $resource::collection($paginator);
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
