<?php

namespace App\Http\Controllers\Api\OpenApi\Responses;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: "ServerError",
    description: "Internal Server Error. An unexpected error occurred while processing the request.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "message",
                type: "string",
                example: "Server Error"
            )
        ]
    )
)]
class ServerErrorResponse
{
}
