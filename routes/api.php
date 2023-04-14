<?php

use App\Http\Controllers\LinkController;
use App\Http\Middleware\AuthStore;
use App\Models\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/register', [AuthController::class, 'createUser']);
Route::post('auth/login', [AuthController::class, 'loginUser']);
Route::middleware([])->group(function () {
        Route::get('link', [LinkController::class, 'index']);
        Route::get('user/link', [LinkController::class, 'getByUser']);
        Route::get('link/{id}', [LinkController::class, 'show']);
        Route::post('link', [LinkController::class, 'createLink']);
        Route::put('link/{id}', [LinkController::class, 'handleUpdate']);
        Route::delete('link/{id}', [LinkController::class, 'destroy']);
        Route::get('user', [LinkController::class, 'getUser']);
});


