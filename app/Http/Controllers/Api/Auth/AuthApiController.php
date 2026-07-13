<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\SettingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends AbstractApiController {
    use ApiResponseTrait;

    public function login(LoginRequest $request): JsonResponse {
        $user =  User::where('email', $request->email)->first();

        try {
            if(!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user->load('role');

            return $this->success([
                'user'          => $user,
                'auth_token'    => $user->createToken($request->device, json_decode($user->role->permissions, true))->plainTextToken
            ], "", 200);

        } catch(\Exception $err) {
            $this->log($request, "Auth", "login", $err);
            return $this->error($err->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function register(RegisterRequest $request): JsonResponse {
        try {
            $setting_default_role = (new SettingService())->get('default_role');

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'role_id'   => $setting_default_role ?? null,
                'password'  => Hash::make($request->password),
            ]);

            $user->load('role');

            return $this->success([
                'user'          => $user,
                'auth_token'    => $user->createToken($request->device, json_decode($user->role->permissions, true))->plainTextToken,
            ], "", Response::HTTP_CREATED);

        } catch(\Exception $err) {
            $this->log($request, "Auth", "register", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(Request $request): JsonResponse {
        return $this->success([
            "user" => $request->user()->load('role')
        ], "");
    }

    public function logout(Request $request): JsonResponse {
        try {
            $request->user()->tokens()->delete();
        } catch(\Exception $err) {
            $this->log($request, "Auth", "logout", $err);
            $this->error($err->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return $this->success([], "Logout was successfully");
    }
}
