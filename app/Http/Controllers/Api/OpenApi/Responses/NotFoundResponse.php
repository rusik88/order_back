<?php

namespace App\Http\Controllers\Api\OpenApi\Responses;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: "NotFound",
    description: "Not Found. The requested resource could not be found.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "message",
                type: "string",
                example: "Not Found"
            )
        ]
    )
)]
class NotFoundResponse
{
}
