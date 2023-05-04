<?php


use App\Http\Controllers\PostController;
use App\Http\Middleware\AuthStore;
use App\Models\User;
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
Route::middleware(['auth:sanctum', AuthStore::class])->group(function () {
    Route::middleware('abilities:' . User::ABILITIES[0])->group(function () {
        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);
    });

    Route::middleware('abilities:' . User::ABILITIES[1])->group(function () {
        Route::post('posts', [PostController::class, 'create']);
    });

    Route::middleware('abilities:' . User::ABILITIES[2])->group(function () {
        Route::put('posts/{id}', [PostController::class, 'update']);
    });

    Route::middleware('abilities:' . User::ABILITIES[3])->group(function () {
        Route::delete('posts/{id}', [PostController::class, 'destroy']);
    });
});
