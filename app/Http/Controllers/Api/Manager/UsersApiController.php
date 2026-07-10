<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Manager\User\StoreUserRequest;
use App\Http\Requests\Api\Manager\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UsersApiController extends AbstractApiController
{
    use ApiResponseTrait;

    #[OA\Get(
        path: "/api/users",
        summary: "Get paginated users list",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Users"],
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
                name: "email",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: 'Admin')
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
                description: "Users list",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "users",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "integer"),
                                            new OA\Property(property: "role_id", type: "integer"),
                                            new OA\Property(property: "name", type: "string"),
                                            new OA\Property(property: "email", type: "string"),
                                            new OA\Property(property: "email_verified_at", type: "string"),
                                            new OA\Property(property: "created_at", type: "string"),
                                            new OA\Property(property: "updated_at", type: "string"),
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
    public function index(Request $request)
    {
        if(!$this->hasAccess($request, 'user:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        $perPage = (int) $request->query('per_page', 10);
        $filter_name = $request->query('email');

        $sortField = $request->query('sort_field', 'id');
        $sortDirection = $request->query('sort_direction', 'desc');

        $users = User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->select('users.*')
            ->when($filter_name, function ($query, $email) {
                $query->where('users.email', 'like', "%{$email}%");
            })
            ->when(
                in_array($sortField, ['name', 'email', 'id', 'created_at', 'role_name']),
                function ($query) use ($sortField, $sortDirection) {

                    if ($sortField === 'role_name') {
                        $query->orderBy('roles.name', $sortDirection);
                    } else {
                        $query->orderBy("users.$sortField", $sortDirection);
                    }
                }
            )
            ->with('role')
            ->paginate($perPage);

        return $this->success([
            'users' => $users->items(),
            'paginate' => [
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'to' => $users->lastItem(),
                'total' => $users->total()
            ]
        ], 200);
    }

    #[OA\Post(
        path: "/api/users",
        summary: "Create user",
        security: [
            ["bearerAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "role_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Administrator"),
                    new OA\Property(property: "email", type: "string", example: "admin"),
                    new OA\Property(property: "role_id", type: "number", example: 3),
                    new OA\Property(property: "password", type: "string", example: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "password")
                ]
            )
        ),
        tags: ["Users"],
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
                                    property: "user",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer"),
                                        new OA\Property(property: "role_id", type: "integer"),
                                        new OA\Property(property: "name", type: "string"),
                                        new OA\Property(property: "email", type: "string"),
                                        new OA\Property(property: "email_verified_at", type: "string"),
                                        new OA\Property(property: "created_at", type: "string"),
                                        new OA\Property(property: "updated_at", type: "string"),
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
    public function store(StoreUserRequest $request)
    {
        if(!$this->hasAccess($request, 'user:create')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $role = Role::find($request->role_id);
            if (!$role) {
                return $this->error('Role not found', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($role->slug === 'super_admin') {
                return $this->error('Not created Super Admin', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'role_id' => $role->id,
                    'password' => Hash::make($request->password),
                ]);

                return $this->success([
                    'user' => $user,
                ], "", Response::HTTP_CREATED);

            } catch(\Exception $err) {
                return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Get user by id",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Users"],
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
                description: "User found",
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function show(Request $request, string $id)
    {
        if(!$this->hasAccess($request, 'user:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        $user = User::find($id);
        $user->load('role');

        if (!$user) {
            return $this->error('User not found', Response::HTTP_NOT_FOUND);
        }

        return $this->success([
            'user' => $user,
        ]);
    }

    #[OA\Patch(
        path: "/api/users/{id}",
        summary: "Update user",
        security: [
            ["bearerAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Administrator"),
                    new OA\Property(property: "email", type: "string", example: "admin"),
                    new OA\Property(property: "role_id", type: "number", example: 3),
                    new OA\Property(property: "password", type: "string", example: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "password")
                ]
            )
        ),
        tags: ["Users"],
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
    public function update(UpdateUserRequest  $request, string $id)
    {
        if(!$this->hasAccess($request, 'user:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return $this->error('User not found', Response::HTTP_NOT_FOUND);
            }

            if($user->role !== null && $user->role->slug !== 'super_admin') {
                $user_data = [
                    "name" => $request->name,
                    "email" => $request->email,
                    "role_id" => $request->role_id,
                ];

                if ($request->filled('password')) {
                    $user_data['password'] = Hash::make($request->password);
                }

                $user->update($user_data);

                return $this->success([
                    'user' => $user,
                ], 'User updated successfully');
            } else {
                return $this->error('Forbidden update user SuperAdmin', Response::HTTP_FORBIDDEN);
            }

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Delete user",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Users"],
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
    public function destroy(Request $request, string $id)
    {
        if(!$this->hasAccess($request, 'user:delete')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->error('User not found', Response::HTTP_NOT_FOUND);
            }

            if($user->role !== null) {
                if($user->role->slug !== 'super_admin') {
                    return $this->deleteUser($user);
                } else {
                    return $this->error('Forbidden delete SuperAdmin', Response::HTTP_FORBIDDEN);
                }
            } else {
                return $this->deleteUser($user);
            }

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function deleteUser(User $user) {
        $user->delete();

        return $this->success(
            [],
            'User deleted successfully'
        );
    }
}
