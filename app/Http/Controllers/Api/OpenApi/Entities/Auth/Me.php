<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Auth;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/auth/me",
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
                        properties: [
                            new OA\Property(
                                property: "user",
                                ref: "#/components/schemas/User"
                            )
                        ],
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
        ),
        new OA\Response(
            ref: "#/components/responses/NotFound",
            response: 404
        )
    ]
)]

class Me extends AbstractOpenApi
{
}
