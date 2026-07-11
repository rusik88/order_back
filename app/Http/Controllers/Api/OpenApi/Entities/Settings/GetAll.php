<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Settings;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/settings",
    summary: "Get all settings",
    security: [
        ["bearerAuth" => []]
    ],
    tags: ["Settings"],
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
                                property: "settings",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "title", type: "string"),
                                        new OA\Property(property: "key", type: "string"),
                                        new OA\Property(property: "value", type: "string"),
                                        new OA\Property(property: "type", type: "string"),
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

class GetAll extends AbstractOpenApi
{
}
