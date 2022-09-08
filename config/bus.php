<?php declare(strict_types = 1);

// phpcs:disable Generic.Files.LineLength

return [
    'command_bus' => [
        'router' => [
            'routes' => [
                \Beagle\Core\Application\Command\User\RegisterUser\RegisterUserCommand::class => \Beagle\Core\Application\Command\User\RegisterUser\RegisterUser::class,
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

            ],
        ],
    ],
];
