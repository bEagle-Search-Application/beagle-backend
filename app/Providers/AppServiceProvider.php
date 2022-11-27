<?php

// phpcs:disable

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
            \Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository::class,
            \Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository::class
        );

        $this->app->bind(
            \Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository::class,
            \Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalRefreshTokenRepository::class
        );

        $this->app->bind(
            \Beagle\Core\Domain\User\UserEmailVerificationRepository::class,
            \Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserEmailVerificationRepository::class
        );

        $this->app->bind(
            \Beagle\Core\Domain\User\UserEmailChangeVerificationRepository::class,
            \Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserEmailChangeVerificationRepository::class
        );

        $this->app->bind(
            \Beagle\Core\Infrastructure\Email\EmailSender::class,
            function () {
                return new \Beagle\Core\Infrastructure\Email\LaravelEmailSender(
                    env('MAIL_FROM_ADDRESS'),
                    env('MAIL_FROM_NAME'),
                );
            }
        );

        $this->app->bind(
            \Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender::class,
            \Beagle\Core\Infrastructure\Email\Verification\LaravelUserVerificationEmailSender::class
        );

        $this->app->bind(
            \Beagle\Core\Infrastructure\Email\Verification\UserEmailChangeVerificationSender::class,
            \Beagle\Core\Infrastructure\Email\Verification\LaravelUserEmailChangeVerificationSender::class
        );

        $this->app->bind(
            \Beagle\Shared\Domain\TokenService::class,
            \Beagle\Shared\Infrastructure\Token\JwtTokenService::class
        );

        $this->app->bind(
            \Beagle\Shared\Bus\EventBus::class,
            static function () {
                $containerLocator = new \Beagle\Shared\Bus\Tactician\LaravelLazyHandlerLocator(
                    config('bus.event_bus.router.routes')
                );

                $commandHandlerMiddleware = new \League\Tactician\Handler\CommandHandlerMiddleware(
                    new \League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor(),
                    $containerLocator,
                    new \League\Tactician\Handler\MethodNameInflector\InvokeInflector()
                );

                return new \Beagle\Shared\Bus\Tactician\TacticianSyncEventBus(
                    $commandHandlerMiddleware
                );
            }
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
