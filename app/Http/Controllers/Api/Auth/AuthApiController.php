<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\AbstractApiController;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class AuthApiController extends AbstractApiController {
    use ApiResponseTrait;

    public function login(Request $request) {

        try {
            $request->validate([
                'email'     => 'required|string|email',
                'password'  => 'required|string',
                'device'    => 'required|string'
            ]);
        } catch (ValidationException $err) {
            return $this->error([], $err->getMessage(), 422);
        }

        $user =  User::where('email', $request->email)->first();

        try {
            if(!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            return $this->success([
                'auth_token' => $user->createToken($request->device, ["user:show"])->plainTextToken
            ], "", 200);

        } catch(\Exception $err) {
            return $this->error([], $err->getMessage(), 422);
        }
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'name'      => ['required', 'string', 'max:255'],
                'password'  => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
                'device'    => ['required', 'string'],
            ]);
        } catch (ValidationException $err) {
            return $this->error([], $err->getMessage(), 422);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->success([
                'user_created' => $user,
                'auth_token' => $user->createToken($request->device, ["user:show"])->plainTextToken,
            ], "", Response::HTTP_CREATED);

        } catch(\Exception $err) {
            return $this->error([], $err->getMessage(), 500);
        }
    }

    public function me(Request $request) {
        return $this->success([
            $request->user()
        ], "", 200);
    }

    public function logout(Request $request) {
        $status = 200;
        try {
            $request->user()->tokens()->delete();
        } catch(\Exception $err) {
            $this->error([], $err->getMessage(), 401);
        }

        return $this->success([], "Logout was successfully", 200);
    }
}
