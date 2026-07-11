<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Settings;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: "/api/settings",
    summary: "Update settings",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["settings"],
            properties: [
                new OA\Property(
                    property: "settings",
                    type: "array",
                    items: new OA\Items(
                        required: ["key", "value"],
                        properties: [
                            new OA\Property(
                                property: "key",
                                type: "string",
                                example: "token_lifetime"
                            ),
                            new OA\Property(
                                property: "value",
                                type: "number",
                                example: "3600"
                            )
                        ],
                        type: "object"
                    )
                )
            ],
            type: "object"
        )
    ),
    tags: ["Settings"],
    responses: [
        new OA\Response(
            response: 200,
            description: "Settings updated successfully",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Settings updated successfully"),
                    new OA\Property(
                        property: "data",
                        type: "array",
                        items: new OA\Items(type: "object"),
                        example: []
                    )
                ],
                type: "object"
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
                            "settings" => [
                                "The settings field is required."
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


class Update extends AbstractOpenApi
{
}
