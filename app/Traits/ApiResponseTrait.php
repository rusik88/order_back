<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $meta
     * @return JsonResponse
     */
    public function success($data = [], $message = '', $code = 200): JsonResponse
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
     * @param array $errors
     * @param mixed $debug (optional debug info for dev)
     * @return JsonResponse
     */
    public function error($message = '', $code = 400,): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => []
        ], $code);
    }
}
