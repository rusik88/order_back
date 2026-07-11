<?php

namespace App\Http\Controllers\Api\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Role",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "slug", type: "string"),
        new OA\Property(
            property: "permissions",
            type: "array",
            items: new OA\Items(type: "string"),
            example: ["order:view", "order:create", "order:update", "order:delete"]
        ),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
    ]
)]
class RoleSchema
{
}
