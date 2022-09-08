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

        $this->app->bind(
            \Beagle\Shared\Bus\CommandBus::class,
            static function () {
                $containerLocator = new \Beagle\Shared\Bus\Tactician\LaravelLazyHandlerLocator(
                    config('bus.command_bus.router.routes')
                );

                $commandHandlerMiddleware = new \League\Tactician\Handler\CommandHandlerMiddleware(
                    new \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor(),
                    $containerLocator,
                    new \League\Tactician\Handler\MethodNameInflector\InvokeInflector()
                );

                return new \Beagle\Shared\Bus\Tactician\TacticianSyncCommandBus(
                    $commandHandlerMiddleware
                );
            }
        );

        $this->app->bind(
            \Beagle\Shared\Bus\QueryBus::class,
            static function () {
                $containerLocator = new \Beagle\Shared\Bus\Tactician\LaravelLazyHandlerLocator(
                    config('bus.query_bus.router.routes')
                );

                $commandHandlerMiddleware = new \League\Tactician\Handler\CommandHandlerMiddleware(
                    new \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor(),
                    $containerLocator,
                    new \League\Tactician\Handler\MethodNameInflector\InvokeInflector()
                );

                return new \Beagle\Shared\Bus\Tactician\TacticianSyncQueryBus(
                    $commandHandlerMiddleware
                );
            }
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
