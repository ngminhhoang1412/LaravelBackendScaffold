<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\LogController;
use App\Http\Middleware\AuthStore;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;

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
        Route::get('logs', [LogController::class, 'index']);
        Route::resource('links',LinkController::class);
        Route::resource('groups',GroupController::class);
        Route::put('links/{id}/groups', [LinkController::class, 'updateLinkGroup']);
        Route::get('groups/{id}/links', [GroupController::class, 'getLinksFromGroup']);
});

