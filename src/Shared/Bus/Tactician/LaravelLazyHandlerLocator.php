<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus\Tactician;

use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

final class LaravelLazyHandlerLocator implements HandlerLocator
{
    private $handlers = [];

    public function __construct(array $commandHandlerMappings)
    {
        foreach ($commandHandlerMappings as $command => $handler) {
            $this->addHandler(
                $command,
                ...\is_array($handler) ? $handler : [$handler]
            );
        }
    }

    public function addHandler(string $commandName, ...$handlers): void
    {
        $this->handlers[$commandName] = function () use ($handlers) {
            if (\count($handlers) === 1) {
                return resolve($handlers[0]);
            }

            return new MultipleHandler(
                ...array_map(
                    function ($handler) {
                        return resolve($handler);
                    },
                    $handlers
                )
            );
        };
    }

    /** @throws MissingHandlerException */
    public function getHandlerForCommand($commandName)
    {
        if (!$this->hasHandlerForCommand($commandName)) {
            throw MissingHandlerException::forCommand($commandName);
        }

        /** @var callable $handler */
        $handler = $this->handlers[$commandName];

        return $handler();
    }

    private function hasHandlerForCommand($commandName): bool
    {
        return \array_key_exists($commandName, $this->handlers);
    }

}
