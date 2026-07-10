<?php

namespace App\Http\Controllers\Api\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "OrderStatus",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "slug", type: "string"),
        new OA\Property(property: "user_id", type: "integer"),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
    ]
)]
class OrderStatusSchema
{
}
