<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends \App\Http\Controllers\Controller {

    use ApiResponseTrait;

    public function getList(Request $request) {
        //Gate::allowIf(fn(User $user) => $user->tokenCan('user:show'));
        $this->success($request->user());
    }
}
