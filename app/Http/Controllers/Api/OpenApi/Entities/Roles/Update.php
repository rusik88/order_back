<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Roles;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: "/api/roles/{id}",
    summary: "Update Role",
    security: [
        ["bearerAuth" => []]
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "slug"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Role name"),
                new OA\Property(property: "slug", type: "string", example: "role_name")
            ]
        )
    ),
    tags: ["Roles"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Role updated",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Role updated successfully"),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "roles",
                                ref: "#/components/schemas/OrderStatus"
                            )
                        ],
                        type: "object"
                    )
                ]
            )
        ),
        new OA\Response(
            response: "422",
            description: "Validation failed. One or more request fields contain invalid values.",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "message",
                        type: "string",
                        example: "The given data was invalid."
                    ),
                    new OA\Property(
                        property: "errors",
                        type: "object",
                        example: [
                            "name" => [
                                "The name field is required."
                            ],
                            "slug" => [
                                "The name field is required."
                            ],
                        ]
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

class Update extends AbstractOpenApi
{
}
