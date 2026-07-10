<?php

namespace App\Http\Controllers\Api\OpenApi\Responses;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: "Unauthorized",
    description: "Unauthenticated. Authentication token is missing or invalid.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "message",
                type: "string",
                example: "Unauthenticated."
            )
        ]
    )
)]
class UnauthorizedResponse
{
}


