<?php
namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Manager\Setting\UpdateSettingRequest;
use App\Services\SettingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class SettingsApiController extends AbstractApiController
{
    use ApiResponseTrait;

    public function __construct(private SettingService $settingService) {}

    #[OA\Get(
        path: "/api/settings/{key}",
        summary: "Get setting value by key",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Settings"],
        parameters: [
            new OA\Parameter(
                name: "key",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Setting found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ""),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "setting",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "key", type: "string"),
                                            new OA\Property(property: "value", type: "string")
                                        ]
                                    )
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Setting not found"
            )
        ]
    )]
    public function get(Request $request, string $key): JsonResponse
    {
        if(!$this->hasAccess($request, 'setting:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        $setting = $this->settingService->get($key);

        if(!$setting) return $this->error('Setting not found', Response::HTTP_NOT_FOUND);

        return $this->success([
            'setting' => [
                'key'   => $key,
                'value' => $setting,
            ],
        ]);
    }

    #[OA\Get(
        path: "/api/settings",
        summary: "Get all settings",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Settings"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Setting found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ""),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "settings",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "title", type: "string"),
                                            new OA\Property(property: "key", type: "string"),
                                            new OA\Property(property: "value", type: "string"),
                                            new OA\Property(property: "type", type: "string"),
                                        ]
                                    )
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Settings not found"
            )
        ]
    )]
    public function all(Request $request): JsonResponse
    {

        if(!$this->hasAccess($request, 'setting:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        $settings = $this->settingService->all();

        if(!$settings) return $this->error('Settings not found', Response::HTTP_NOT_FOUND);

        return $this->success([
            'settings' => $settings,
        ]);
    }

    #[OA\Put(
        path: "/api/settings",
        summary: "Update settings",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["settings"],
                properties: [
                    new OA\Property(
                        property: "settings",
                        type: "array",
                        items: new OA\Items(
                            required: ["key", "value"],
                            properties: [
                                new OA\Property(
                                    property: "key",
                                    type: "string",
                                    example: "token_lifetime"
                                ),
                                new OA\Property(
                                    property: "value",
                                    type: "number",
                                    example: "3600"
                                )
                            ],
                            type: "object"
                        )
                    )
                ],
                type: "object"
            )
        ),
        tags: ["Settings"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Settings updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Settings updated successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(type: "object"),
                            example: []
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'setting:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $settings = $request->settings;
            if(!empty($settings)) {
                $this->settingService->setAll($settings);
            }
        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->success([], 'Settings updated successfully');
    }
}
