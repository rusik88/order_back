<?php

namespace App\Http\Controllers\Api\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "role_id", type: "integer"),
        new OA\Property(property: "email", type: "string"),
        new OA\Property(property: "email_verified_at", type: "string"),

        new OA\Property(
            property: "role",
            ref: "#/components/schemas/Role"
        ),

        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
    ]
)]
class UserSchema
{
}
