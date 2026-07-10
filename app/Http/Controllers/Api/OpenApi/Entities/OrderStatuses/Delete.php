<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\OrderStatuses;

use App\Http\Controllers\Api\AbstractApiController;
use OpenApi\Attributes as OA;

#[OA\Delete(
    path: "/api/order_statuses/{id}",
    summary: "Delete Order Status by id",
    security: [
        ["bearerAuth" => []]
    ],
    tags: ["OrderStatus"],
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
            response: 201,
            description: "Order Status created",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Order Status deleted successfully"),
                    new OA\Property(
                        property: "data",
                        type: "array",
                        items: new OA\Items()
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

class Delete extends AbstractApiController
{
}
