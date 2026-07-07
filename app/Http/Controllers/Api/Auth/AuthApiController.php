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
use OpenApi\Attributes as OA;

class AuthApiController extends AbstractApiController {
    use ApiResponseTrait;

    #[OA\Post(
        path: "/api/auth/login",
        description: "Authenticate user and return token",
        summary: "Login user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "device"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "test@test.com"),
                    new OA\Property(property: "password", type: "string", example: "password"),
                    new OA\Property(property: "device", type: "string", example: "web"),
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ''),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "auth_token", type: "string", example: "BEARER_TOKEN"),
                                new OA\Property(
                                    property: "user",
                                    properties: [
                                        new OA\Property(property: "id", type: "number", example: 2),
                                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                                        new OA\Property(property: "email", type: "string", example: "test@test.com"),
                                        new OA\Property(property: "email_verified_at", type: "string", example: null),
                                        new OA\Property(property: "created_at", type: "datetime"),
                                        new OA\Property(property: "updated_at", type: "datetime"),

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
                'user' => $user,
                'auth_token' => $user->createToken($request->device, json_decode($user->role->permissions, true))->plainTextToken
            ], "", 200);

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[OA\Post(
        path: "/api/auth/register",
        summary: "Register user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "device"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", example: "test@stest.com"),
                    new OA\Property(property: "password", type: "string", example: "password"),
                    new OA\Property(property: "device", type: "string", example: "password"),
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ''),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "auth_token", type: "string", example: "BEARER_TOKEN"),
                                new OA\Property(
                                    property: "user",
                                    properties: [
                                        new OA\Property(property: "id", type: "number", example: 2),
                                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                                        new OA\Property(property: "email", type: "string", example: "test@test.com"),
                                        new OA\Property(property: "email_verified_at", type: "string", example: null),
                                        new OA\Property(property: "created_at", type: "datetime"),
                                        new OA\Property(property: "updated_at", type: "datetime"),

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
    public function register(RegisterRequest $request): JsonResponse {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->success([
                'user' => $user,
                'auth_token' => $user->createToken($request->device, ["user:show"])->plainTextToken,
            ], "", Response::HTTP_CREATED);

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/auth/me",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ''),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "number", example: 2),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "test@test.com"),
                                new OA\Property(property: "email_verified_at", type: "string", example: null),
                                new OA\Property(property: "created_at", type: "datetime"),
                                new OA\Property(property: "updated_at", type: "datetime"),
                            ],
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
    public function me(Request $request): JsonResponse {
        return $this->success([
            "user" => $request->user()->load('role')
        ], "");
    }

    #[OA\Post(
        path: "/api/auth/logout",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ''),
                        new OA\Property(
                            property: "data",
                            properties: [],
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request): JsonResponse {
        try {
            $request->user()->tokens()->delete();
        } catch(\Exception $err) {
            $this->error($err->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        return $this->success([], "Logout was successfully");
    }
}
