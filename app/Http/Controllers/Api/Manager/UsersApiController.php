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
