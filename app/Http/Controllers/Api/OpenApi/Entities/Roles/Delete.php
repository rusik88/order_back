<?php

namespace App\Http\Controllers\Api\OpenApi\Entities\Roles;

use App\Http\Controllers\Api\OpenApi\AbstractOpenApi;
use OpenApi\Attributes as OA;

#[OA\Delete(
    path: "/api/roles/{id}",
    summary: "Delete Role by id",
    security: [
        ["bearerAuth" => []]
    ],
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
            response: 201,
            description: "Role created",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Role deleted successfully"),
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

class Delete extends AbstractOpenApi
{
}
