<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Models\Role;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class RoleApiController extends AbstractApiController
{
    use ApiResponseTrait;

    #[OA\Get(
        path: "/api/roles",
        summary: "Get paginated roles list",
        tags: ["Roles"],
        security: [
            ["bearerAuth" => []]
        ],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 10)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Roles list",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "roles",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "integer"),
                                            new OA\Property(property: "name", type: "string"),
                                            new OA\Property(property: "slug", type: "string"),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "paginate",
                                    properties: [
                                        new OA\Property(property: "current_page", type: "integer"),
                                        new OA\Property(property: "from", type: "integer"),
                                        new OA\Property(property: "last_page", type: "integer"),
                                        new OA\Property(property: "per_page", type: "integer"),
                                        new OA\Property(property: "to", type: "integer"),
                                        new OA\Property(property: "total", type: "integer"),
                                    ],
                                    type: "object"
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $roles = Role::paginate($perPage);

        return $this->success([
            'roles' => $roles->items(),
            'paginate' => [
                'current_page' => $roles->currentPage(),
                'from' => $roles->firstItem(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'to' => $roles->lastItem(),
                'total' => $roles->total()
            ]
        ], 200);
    }

    #[OA\Post(
        path: "/api/roles",
        summary: "Create role",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Roles"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "slug"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Administrator"),
                    new OA\Property(property: "slug", type: "string", example: "admin"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["users.view", "users.create"]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Role created",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Role created successfully"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "role",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer"),
                                        new OA\Property(property: "name", type: "string"),
                                        new OA\Property(property: "slug", type: "string"),
                                    ]
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = Role::create([
                    "name" => $request->name,
                    "slug" => $request->slug,
                    "permissions" => json_encode($request->permissions)
                ]
            );

            return $this->success([
                'role' => $role,
            ], 'Role created successfully', Response::HTTP_CREATED);
        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/roles/{id}",
        summary: "Get role by id",
        tags: ["Roles"],
        security: [
            ["bearerAuth" => []]
        ],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Role found",
            ),
            new OA\Response(
                response: 404,
                description: "Role not found"
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->error('Role not found', Response::HTTP_NOT_FOUND);
        }

        return $this->success([
            'role' => $role,
        ]);
    }

    #[OA\Patch(
        path: "/api/roles/{id}",
        summary: "Update role",
        tags: ["Roles"],
        security: [
            ["bearerAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Admin"),
                    new OA\Property(property: "slug", type: "string", example: "admin"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["users.view"]
                    )
                ]
            )
        ),
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Updated"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return $this->error('Role not found', Response::HTTP_NOT_FOUND);
            }

            $role->update([
                "name" => $request->name,
                "slug" => $request->slug,
                "permissions" => json_encode($request->permissions)
            ]);

            return $this->success([
                'role' => $role,
            ], 'Role updated successfully');

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: "/api/roles/{id}",
        summary: "Delete role",
        tags: ["Roles"],
        security: [
            ["bearerAuth" => []]
        ],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return $this->error('Role not found', Response::HTTP_NOT_FOUND);
            }

            $role->delete();

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->success(
            [],
            'Role deleted successfully'
        );
    }
}
