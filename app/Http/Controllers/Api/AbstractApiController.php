<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        title: "Order Back API"
    ),
    servers: [
        new OA\Server(url: "http://order-back.loc")
    ]
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]

abstract class AbstractApiController extends Controller
{

}
