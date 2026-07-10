<?php

namespace App\Http\Controllers\Api\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Order",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "user_id", type: "integer"),
        new OA\Property(property: "order_status_id", type: "integer"),
        new OA\Property(property: "total", type: "number"),
        new OA\Property(property: "comment", type: "string"),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),

        new OA\Property(
            property: "order_status",
            ref: "#/components/schemas/OrderStatus"
        )
    ]
)]
class OrderSchema
{
}
