<?php

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'getList'])->middleware([
    'auth:sanctum',
    'ability:user:show'
]);

//Auth Tokens
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::get('/auth/me', [AuthApiController::class, 'me'])->middleware('auth:sanctum');;
Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
