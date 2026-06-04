<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends \App\Http\Controllers\Controller {
    public $json = [
        'success' => true,
        'message' => '',
        'data' => []
    ];

    public function getList(Request $request) {
        //Gate::allowIf(fn(User $user) => $user->tokenCan('user:show'));

        $this->json['data'] = [
            "user" => $request->user()
        ];
        return response()->json($this->json, 200);
    }
}
