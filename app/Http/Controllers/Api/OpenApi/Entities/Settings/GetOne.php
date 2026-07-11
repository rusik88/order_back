<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Settings;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/settings/{key}",
    summary: "Get setting value by key",
    security: [
        ["bearerAuth" => []]
    ],
    tags: ["Settings"],
    parameters: [
        new OA\Parameter(
            name: "key",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "string")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Setting found",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: ""),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "setting",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "key", type: "string"),
                                        new OA\Property(property: "value", type: "string")
                                    ]
                                )
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

class GetOne extends AbstractOpenApi
{
}
