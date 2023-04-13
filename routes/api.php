<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\LinkController;
use App\Http\Middleware\AuthStore;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;

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
    Route::middleware('abilities:' . User::class)->group(function () {
        Route::get('link', [LinkController::class, 'index']);
        Route::get('link/{id}', [LinkController::class, 'show']);
        Route::post('link', [LinkController::class, 'createLink']);
        Route::put('link/{id}', [LinkController::class, 'update']);
        Route::delete('link/{id}', [LinkController::class, 'destroy']);
    });
});

