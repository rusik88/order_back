<?php

use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Manager\OrderApiController;
use App\Http\Controllers\Api\Manager\OrderStatusApiController;
use App\Http\Controllers\Api\Manager\RoleApiController;
use App\Http\Controllers\Api\Manager\SettingsApiController;
use App\Http\Controllers\Api\Manager\UsersApiController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*Route::post('/users', [UserController::class, 'getList'])->middleware([
    'auth:sanctum',
    'ability:user:show'
]);*/

//Auth Tokens
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::get('/auth/me', [AuthApiController::class, 'me'])->middleware('auth:sanctum');
Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

//Roles Routes
Route::apiResource('roles', RoleApiController::class)->middleware('auth:sanctum');

//Users Routes
Route::apiResource('users', UsersApiController::class)->middleware('auth:sanctum');

//Order Statuses
Route::apiResource('order_statuses', OrderStatusApiController::class)->middleware('auth:sanctum');

//Order
Route::apiResource('orders', OrderApiController::class)->middleware('auth:sanctum');

//Settings Routes
Route::prefix('settings')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [SettingsApiController::class, 'all']);
    Route::put('/', [SettingsApiController::class, 'update']);
    Route::get('/{key}', [SettingsApiController::class, 'get']);
});
