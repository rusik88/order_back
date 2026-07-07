<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Manager\Role\StoreRoleRequest;
use App\Http\Requests\Api\Manager\Role\UpdateRoleRequest;
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
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Roles"],
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
            ),
            new OA\Parameter(
                name: "name",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: '')
            ),
            new OA\Parameter(
                name: "sort_field",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: 'name')
            ),
            new OA\Parameter(
                name: "sort_direction",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: 'asc')
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
        if($this->hasAccess($request, 'role:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        $perPage = (int) $request->query('per_page', 10);
        $filter_name = $request->query('name');

        $sortField = $request->query('sort_field', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');

        $query = Role::query()
            ->when($filter_name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(in_array($sortField, ['name', 'slug', 'id', 'created_at']), function ($query) use ($sortField, $sortDirection) {
                $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
            });

        $roles = $perPage === -1 ? $query->get() : $query->paginate($perPage);

        return $this->success([
            'roles' => $perPage !== -1 ? $roles->items() : $roles,
            'paginate' => [
                'current_page' => $perPage !== -1 ? $roles->currentPage() : 1,
                'from' => $perPage !== -1 ? $roles->firstItem() : 1,
                'last_page' => $perPage !== -1 ? $roles->lastPage() : 1,
                'per_page' => $perPage !== -1 ? $roles->perPage() : -1,
                'to' => $perPage !== -1 ? $roles->lastItem() : count($roles),
                'total' => $perPage !== -1 ? $roles->total() : count($roles)
            ]
        ], 200);
    }

    #[OA\Post(
        path: "/api/roles",
        summary: "Create role",
        security: [
            ["bearerAuth" => []]
        ],
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
        tags: ["Roles"],
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
                                    properties: [
                                        new OA\Property(property: "id", type: "integer"),
                                        new OA\Property(property: "name", type: "string"),
                                        new OA\Property(property: "slug", type: "string"),
                                    ],
                                    type: "object"
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
        if($this->hasAccess($request, 'role:create')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            if($request->slug !== 'super_admin') {
                $role = Role::create([
                        "name" => $request->name,
                        "slug" => $request->slug,
                        "permissions" => json_encode($request->permissions)
                    ]
                );
                return $this->success([
                    'role' => $role,
                ], 'Role created successfully', Response::HTTP_CREATED);
            } else {
                return $this->error('Forbidden create SuperAdmin', Response::HTTP_FORBIDDEN);
            }


        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/roles/{id}",
        summary: "Get role by id",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Roles"],
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
    public function show(Request $request, string $id): JsonResponse
    {
        if($this->hasAccess($request, 'role:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
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
        tags: ["Roles"],
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
    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        if($this->hasAccess($request, 'role:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $role = Role::find($id);

            if (!$role) {
                return $this->error('Role not found', Response::HTTP_NOT_FOUND);
            }

            if($role->slug !== 'super_admin') {
                $role->update([
                    "name" => $request->name,
                    "slug" => $request->slug,
                    "permissions" => json_encode($request->permissions)
                ]);

                return $this->success([
                    'role' => $role,
                ], 'Role updated successfully');
            } else {
                return $this->error('Forbidden update SuperAdmin', Response::HTTP_FORBIDDEN);
            }

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: "/api/roles/{id}",
        summary: "Delete role",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Roles"],
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
    public function destroy(Request $request, string $id): JsonResponse
    {
        if($this->hasAccess($request, 'role:delete')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $role = Role::find($id);

            if (!$role) {
                return $this->error('Role not found', Response::HTTP_NOT_FOUND);
            }

            if($role->slug !== 'super_admin') {
                $role->delete();

                return $this->success(
                    [],
                    'Role deleted successfully'
                );
            } else {
                return $this->error('Forbidden delete SuperAdmin', Response::HTTP_FORBIDDEN);
            }


        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}
