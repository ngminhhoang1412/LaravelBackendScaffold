<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TestController;
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
    // Ability: 'basic-info'
    Route::middleware('abilities:' . User::ABILITIES[0])->group(function () {
        // Sites
        Route::get('sites', [SiteController::class, 'index']);
        Route::get('sites/{id}', [SiteController::class, 'show']);
        Route::post('sites', [SiteController::class, 'create']);
        Route::put('sites/{id}', [SiteController::class, 'update']);
        Route::delete('sites/{id}', [SiteController::class, 'destroy']);

        // Services
        Route::get('services', [OrderTypeController::class, 'index']);
        Route::get('services/{id}', [OrderTypeController::class, 'show']);

        // Workers
        // Should work again on worker update API
        Route::get('workers', [WorkerController::class, 'index']);
        Route::get('workers/{id}', [WorkerController::class, 'show']);
        Route::put('workers/{id}', [WorkerController::class, 'update']);
    });
    // Ability: 'order-info'
    Route::middleware('abilities:' . User::ABILITIES[1])->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
    });
    // Ability: 'order-portal'/'order-worker'
    // TODO: should separate these 2 roles (order edit/order cancel)
//    Route::group(['middleware' => [
//        'abilities:' . User::ABILITIES[2],
//        'abilities:' . User::ABILITIES[3],
//    ]], function (){
        Route::put('orders/{id}', [OrderController::class, 'update']);
//    });
    // Ability: 'order-portal'
    Route::middleware('abilities:' . User::ABILITIES[2])->group(function () {
        Route::post('orders', [OrderController::class, 'create']);
    });
    // Ability: resource-info
    Route::middleware('abilities:' . User::ABILITIES[4])->group(function () {
        // TODO: why there isn't any ResourceController
//        Route::put('resources/{workerId}', [MailController::class, 'adjust']);
        Route::put('mails/{mail}', [MailController::class, 'update']);

//        Route::get('resources', [ResourceController::class, 'index']);
//        Route::put('resources', [ResourceController::class, 'update']);
    });

});

Route::get('test', [TestController::class, 'test']);
