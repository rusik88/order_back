<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

abstract class AbstractApiController extends Abstra
{
    protected $json = [
        'success' => true,
        'message' => '',
        'data' => []
    ];

    protected function response($status = 200) {
        return response()->json($this->json, $status);
    }


}
