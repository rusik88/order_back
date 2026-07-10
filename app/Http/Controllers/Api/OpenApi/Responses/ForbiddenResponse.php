<?php

namespace App\Http\Controllers\Api\OpenApi\Responses;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: "Forbidden",
    description: "Forbidden. You do not have permission to access this resource.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "success",
                type: "boolean",
                example: false
            ),
            new OA\Property(
                property: "message",
                type: "string",
                example: "Access denied."
            ),
            new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items()
            )
        ]
    )
)]
class ForbiddenResponse
{
}


