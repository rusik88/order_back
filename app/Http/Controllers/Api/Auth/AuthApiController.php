<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\AbstractApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class AuthApiController extends AbstractApiController {
    public $json = [
        'success' => true,
        'message' => '',
        'data' => []
    ];

    public function login(Request $request) {

        try {
            $request->validate([
                'email'     => 'required|string|email',
                'password'  => 'required|string',
                'device'    => 'required|string'
            ]);
        } catch (ValidationException $err) {
            $this->json['message'] = $err->getMessage();
            $this->response(422);
        }

        $user =  User::where('email', $request->email)->first();

        try {
            if(!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $this->json['data'] = [
                'auth_token' => $user->createToken($request->device, ["user:show"])->plainTextToken,
            ];

            $this->response(200);

        } catch(\Exception $err) {
            $this->json['message'] = $err->getMessage();
            $this->response();
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
            $this->json['message'] = $err->getMessage();
            $this->response(422);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $this->json['data'] = [
                'user_created' => $user,
                'auth_token' => $user->createToken($request->device, ["user:show"])->plainTextToken,
            ];

            $this->response(Response::HTTP_CREATED);

        } catch(\Exception $err) {
            $this->json['message'] = $err->getMessage();
            $this->response(500);
        }
    }

    public function me(Request $request) {
        $this->json["data"] = $request->user();
        $this->response(200);
    }

    public function logout(Request $request) {
        $status = 200;
        try {
            $request->user()->tokens()->delete();
        } catch(\Exception $err) {
            $this->json['success'] = false;
            $status = 401;
            $this->json['message'] = $err->getMessage();
        }

        $this->response($status);
    }
}
