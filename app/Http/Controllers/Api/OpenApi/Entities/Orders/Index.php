<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Orders;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/orders",
    summary: "Get paginated orders list",
    security: [
        ["bearerAuth" => []]
    ],
    tags: ["Orders"],
    parameters: [
        new OA\Parameter(
            name: "page",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "integer", example: 1)
        ),
        new OA\Parameter(
            name: "per_page",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "integer", example: 10)
        ),
        new OA\Parameter(
            name: "name",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "string", example: '')
        ),
        new OA\Parameter(
            name: "sort_field",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "string", example: 'id')
        ),
        new OA\Parameter(
            name: "sort_direction",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "string", example: 'desc')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Orders list",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "orders",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: "order",
                                            ref: "#/components/schemas/Order"
                                        )
                                    ]
                                )
                            ),
                            new OA\Property(
                                property: "paginate",
                                ref: "#/components/schemas/Pagination"
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
class Index extends AbstractOpenApi
{
}
