<?php

namespace App\Http\Controllers\Api\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Pagination",
    title: "Pagination",
    description: "Pagination information",
    properties: [
        new OA\Property(
            property: "current_page",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "from",
            type: "integer",
            example: 1
        ),
        new OA\Property(
            property: "last_page",
            type: "integer",
            example: 5
        ),
        new OA\Property(
            property: "per_page",
            type: "integer",
            example: 10
        ),
        new OA\Property(
            property: "to",
            type: "integer",
            example: 10
        ),
        new OA\Property(
            property: "total",
            type: "integer",
            example: 47
        ),
    ],
    type: "object"
)]
class PaginationSchema
{
}
