<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Auth;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/auth/register",
    summary: "Register user",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "device"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "John Doe"),
                new OA\Property(property: "email", type: "string", example: "test@stest.com"),
                new OA\Property(property: "password", type: "string", example: "password"),
                new OA\Property(property: "device", type: "string", example: "password"),
            ]
        )
    ),
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
                            new OA\Property(property: "auth_token", type: "string", example: "BEARER_TOKEN"),
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
                            "email" => [
                                "The email field is required."
                            ],
                            "password" => [
                                "The password field is required."
                            ],
                            "device" => [
                                "The device field is required."
                            ]
                        ],
                        additionalProperties: new OA\AdditionalProperties(
                            type: "array",
                            items: new OA\Items(type: "string")
                        )
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

class Register extends AbstractOpenApi
{
}
