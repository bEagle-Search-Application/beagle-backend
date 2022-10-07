<?php

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
        Route::post('/verify/{token}',
            [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\AcceptUserVerificationEmailController::class,
                'execute'
            ]
        )->name('api.users-verify');
    });

    Route::prefix('auth')->group(function () {
        Route::get('/login',
            [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\LoginController::class,
                'execute'
            ]
        )->name('api.login');
    });

    Route::post('/token/refresh',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\RefreshTokenController::class,
            'execute'
        ]
    )->name('api.token-refresh')
         ->middleware(['verify.refresh.token']);

    Route::post('/register',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\RegisterUserController::class,
            'execute'
        ]
    )->name('api.register');

    Route::post('/logout',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\LogoutController::class,
            'execute'
        ]
    )->name('api.logout')
     ->middleware(['verify.access.token']);
});
