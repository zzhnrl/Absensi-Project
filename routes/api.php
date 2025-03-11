<?php

use App\Helpers\Generate;
use App\Jobs\BroadcastPusherAssetBaruDitolakNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\StatusPengesahan;
use App\Libraries\Pusher;
use App\Models\User;
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

Route::post('do-login', [App\Http\Controllers\Api\AuthController::class, 'doLogin']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('reset-password/{user_uuid}', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    Route::post('do-logout', [App\Http\Controllers\Api\AuthController::class, 'doLogout']);
    Route::get('get-user-session-information', [App\Http\Controllers\Api\AuthController::class, 'getUserSessionInformation']);
    Route::put('update-profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
    Route::put('update-password', [App\Http\Controllers\Api\AuthController::class, 'updatePassword']);
    Route::post('upload-file', [App\Http\Controllers\Api\FileStorageController::class, 'uploadImage']);

    Route::prefix('role-permission')->group(function () {
        Route::get('{uuid?}', [App\Http\Controllers\Api\RolePermissionController::class, 'get']);
        Route::put('', [App\Http\Controllers\Api\RolePermissionController::class, 'update']);
    });

    Route::prefix('role')->group(function () {
        Route::get('{role_uuid?}', [App\Http\Controllers\Api\RoleController::class, 'get']);
        Route::post('', [App\Http\Controllers\Api\RoleController::class, 'store']);
        Route::put('{role_uuid}', [App\Http\Controllers\Api\RoleController::class, 'update']);
        Route::delete('{role_uuid}', [App\Http\Controllers\Api\RoleController::class, 'delete']);
    });
});

