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
    Route::prefix('auth')->group(function () {
        Route::get('/login',
            [
                \Beagle\Core\Infrastructure\Http\Api\Controllers\LoginController::class,
                'execute'
            ]
        )->name('api.login');
    });

    Route::post('/register',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\RegisterUserController::class,
            'execute'
        ]
    )->name('api.register');
});

Route::middleware(['auth.jwt'])->group(function(){
    Route::post('/logout',
        [
            \Beagle\Core\Infrastructure\Http\Api\Controllers\LogoutController::class,
            'execute'
        ]
    )->name('api.logout');
});
