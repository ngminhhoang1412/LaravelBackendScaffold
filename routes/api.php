<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AbsenceRequestController;
use App\Http\Middleware\AuthStore;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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

Route::post('auth/confirm-email', [AuthController::class, 'confirmEmail']);
Route::get('auth/send-email', [AuthController::class, 'sendRegisterMail']);
Route::get('auth/expired-time', [AuthController::class, 'checkExpiredTime']);
Route::post('auth/register', [AuthController::class, 'createUser']);
Route::post('auth/login', [AuthController::class, 'loginUser']);
Route::middleware(['auth:sanctum', AuthStore::class])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::post('permission/assign', [PermissionController::class, 'assignPermissionToRole']);
    Route::post('role/assign', [RoleController::class, 'assignRoleToUser']);
    Route::put('user/{id}/salary', [UserController::class, 'updateSalary']);
    
    Route::resource('absence-types', AbsenceController::class);
    Route::resource('absence-request', AbsenceRequestController::class);
});
