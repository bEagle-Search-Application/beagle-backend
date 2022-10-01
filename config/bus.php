<?php declare(strict_types = 1);

// phpcs:disable Generic.Files.LineLength

return [
    'command_bus' => [
        'router' => [
            'routes' => [
                \Beagle\Core\Application\Command\User\RegisterUser\RegisterUserCommand::class => \Beagle\Core\Application\Command\User\RegisterUser\RegisterUser::class,
                \Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmailCommand::class => \Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmail::class,
                \Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand::class => \Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmail::class,
                \Beagle\Core\Application\Command\User\RefreshToken\RefreshTokenCommand::class => \Beagle\Core\Application\Command\User\RefreshToken\RefreshToken::class,
            ],
        ],
    ],
    'query_bus' => [
        'router' => [
            'routes' => [
                \Beagle\Core\Application\Query\User\Login\LoginQuery::class => \Beagle\Core\Application\Query\User\Login\Login::class,
            ],
        ],
    ],
    'event_bus' => [
        'router' => [
            'routes' => [
                \Beagle\Core\Domain\User\Event\UserCreated::class => [
                    \Beagle\Core\Application\Listener\User\SendEmailVerificationEmail\SendEmailVerificationEmail::class
                ]
            ],
        ],
    ],
];
