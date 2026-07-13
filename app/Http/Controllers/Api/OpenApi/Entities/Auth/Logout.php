<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Auth;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/auth/logout",
    security: [["bearerAuth" => []]],
    tags: ["Auth"],
    responses: [
        new OA\Response(
            response: 200,
            description: "Success",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: ''),
                    new OA\Property(
                        property: "data",
                        properties: [],
                        type: "object"
                    )
                ]
            )
        ),
        new OA\Response(
            ref: "#/components/responses/Unauthorized",
            response: 401
        ),
        new OA\Response(
            ref: "#/components/responses/Forbidden",
            response: 403
        ),
        new OA\Response(
            ref: "#/components/responses/ServerError",
            response: 500
        )
    ]
)]

class Logout extends AbstractOpenApi
{
}
