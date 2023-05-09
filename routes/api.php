<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\AuthStore;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

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
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::post('groups', [GroupController::class, 'store']);
    Route::get('groups', [GroupController::class, 'index']);
    Route::delete('groups/{id}', [GroupController::class, 'destroy']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    Route::post('teams', [TeamController::class, 'store']);
    Route::get('teams', [TeamController::class, 'index']);
    Route::delete('teams/{id}', [TeamController::class, 'destroy']);
    Route::post('permission/assign', [PermissionController::class, 'assignPermissionToRole']);
    Route::post('role/assign', [RoleController::class, 'assignRoleToUser']);
});
