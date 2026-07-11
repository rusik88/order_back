<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Users;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/users",
    summary: "Create Order",
    security: [
        ["bearerAuth" => []]
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "role_id", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "User name"),
                new OA\Property(property: "role_id", type: "integer", example: 2),
                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                new OA\Property(property: "password", type: "string", example: "12345678"),
                new OA\Property(property: "password_confirmation", type: "string", example: "12345678"),
            ]
        )
    ),
    tags: ["Users"],
    responses: [
        new OA\Response(
            response: 201,
            description: "User created",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "User created successfully"),
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
                            "role_id" => [
                                "The selected user role id is invalid."
                            ],
                            "email" => [
                                "The total field is required."
                            ],
                            "password" => [
                                "The password field is required."
                            ],
                            "password_confirmation" => [
                                "The password_confirmation field is required."
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
