<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Roles;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/roles",
    summary: "Get paginated roles list",
    security: [
        ["bearerAuth" => []]
    ],
    tags: ["Roles"],
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
            schema: new OA\Schema(type: "string", example: 'name')
        ),
        new OA\Parameter(
            name: "sort_direction",
            in: "query",
            required: false,
            schema: new OA\Schema(type: "string", example: 'asc')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Roles list",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(
                        property: "data",
                        properties: [
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: "role",
                                            ref: "#/components/schemas/Role"
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
