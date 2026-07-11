<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Orders;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/orders",
    summary: "Create Order",
    security: [
        ["bearerAuth" => []]
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "order_staus_id", "total"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Order name"),
                new OA\Property(property: "order_status_id", type: "integer", example: 2),
                new OA\Property(property: "total", type: "number", example: 250.50),
                new OA\Property(property: "comment", type: "string", example: "Your comment...", nullable: true),
            ]
        )
    ),
    tags: ["Orders"],
    responses: [
        new OA\Response(
            response: 201,
            description: "Order created",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Order created successfully"),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "order",
                                ref: "#/components/schemas/Order"
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
                            "order_status_id" => [
                                "The selected order status id is invalid."
                            ],
                            "total" => [
                                "The total field is required."
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

class Store extends AbstractOpenApi
{
}
