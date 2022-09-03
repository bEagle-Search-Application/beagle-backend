<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus\Tactician;

final class MultipleHandler
{
    private $handlers;

    public function __construct(...$listeners)
    {
        $this->handlers = $listeners;
    }

    public function __invoke($command)
    {
        foreach ($this->handlers as $handler) {
            $handler($command);
        }
    }

}
