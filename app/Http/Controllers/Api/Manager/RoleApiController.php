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

class RoleApiController extends AbstractApiController
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'role:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $perPage = (int) $request->query('per_page', 10);
            $filter_name = $request->query('name');

            $sortField = $request->query('sort_field', 'id');
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
                    'current_page'  => $perPage !== -1 ? $roles->currentPage() : 1,
                    'from'          => $perPage !== -1 ? $roles->firstItem() : 1,
                    'last_page'     => $perPage !== -1 ? $roles->lastPage() : 1,
                    'per_page'      => $perPage !== -1 ? $roles->perPage() : -1,
                    'to'            => $perPage !== -1 ? $roles->lastItem() : count($roles),
                    'total'         => $perPage !== -1 ? $roles->total() : count($roles)
                ]
            ], 200);
        } catch(\Exception $err) {
            $this->log($request, "Role", "index", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'role:create')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            if($request->slug !== 'super_admin') {
                $role = Role::create([
                        "name"          => $request->name,
                        "slug"          => $request->slug,
                        "permissions"   => json_encode($request->permissions)
                    ]
                );
                return $this->success([
                    'role' => $role,
                ], 'Role created successfully', Response::HTTP_CREATED);
            } else {
                return $this->error('Forbidden create SuperAdmin', Response::HTTP_FORBIDDEN);
            }


        } catch(\Exception $err) {
            $this->log($request, "Role", "store", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        if(!$this->hasAccess($request, 'role:read')) {
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

        return $this->success([
            'role' => $role,
        ]);
        } catch(\Exception $err) {
            $this->log($request, "Role", "show", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        if(!$this->hasAccess($request, 'role:update')) {
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
                    "name"          => $request->name,
                    "slug"          => $request->slug,
                    "permissions"   => json_encode($request->permissions)
                ]);

                return $this->success([
                    'role' => $role,
                ], 'Role updated successfully');
            } else {
                return $this->error('Forbidden update SuperAdmin', Response::HTTP_FORBIDDEN);
            }

        } catch(\Exception $err) {
            $this->log($request, "Role", "update", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if(!$this->hasAccess($request, 'role:delete')) {
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
            $this->log($request, "Role", "destroy", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
