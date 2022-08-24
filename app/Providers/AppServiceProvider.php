<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Beagle\Core\Domain\User\UserRepository::class,
            \Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository::class
        );

        $this->app->bind(
            \Beagle\Shared\Application\Auth\AuthService::class,
            \Beagle\Shared\Infrastructure\Auth\JwtAuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
