<?php

// phpcs:disable

use Illuminate\Support\Facades\Route;

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
Route::middleware(['api'])->group(function(){
    Route::prefix('users')->group(function () {
        Route::post('{userId}/verify/{token}',
            [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\User\AcceptUserVerificationEmailController::class,
                'execute'
            ]
        )->name('api.users-verify');

        Route::get(
            '/{userId}', [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\User\GetUserController::class,
                'execute',
            ]
        )->name('api.get-user')->middleware(['verify.access.token']);

        Route::put(
            '/{userId}', [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\User\EditUserController::class,
                'execute',
            ]
        )->name('api.edit-user')->middleware(['verify.access.token']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login',
            [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\User\LoginController::class,
                'execute'
            ]
        )->name('api.login');
    });

    Route::post('/token/refresh',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\User\RefreshTokenController::class,
            'execute'
        ]
    )->name('api.token-refresh')->middleware(['verify.refresh.token']);

    Route::post('/register',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\User\RegisterUserController::class,
            'execute'
        ]
    )->name('api.register');

    Route::post('/logout',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\User\LogoutController::class,
            'execute'
        ]
    )->name('api.logout')->middleware(['verify.access.token']);
});
