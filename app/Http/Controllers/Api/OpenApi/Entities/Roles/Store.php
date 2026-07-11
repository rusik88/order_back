<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Roles;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/roles",
    summary: "Create Role Status",
    security: [
        ["bearerAuth" => []]
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "slug"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Role name"),
                new OA\Property(property: "slug", type: "string", example: "role_name"),
            ]
        )
    ),
    tags: ["Roles"],
    responses: [
        new OA\Response(
            response: 201,
            description: "Role Status created",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Role created successfully"),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "role",
                                ref: "#/components/schemas/Role"
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
                                "The slug field is required."
                            ]
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

class Store extends AbstractOpenApi
{
}
