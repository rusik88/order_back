<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function success(array $data = [], string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error JSON response
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function error(string $message = '', int $code = 400,): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => []
        ], $code);
    }

    /**
     * Check on access ability
     *
     * @param Request $request
     * @param string $ability
     * @return bool
     */
    public function hasAccess(Request $request, string $ability): bool
    {
        return !$request->user()->tokenCan($ability);
    }
}
