<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus;

abstract class CommandHandler
{
    final public function __invoke(Command $command): void
    {
        $this->handle($command);
    }

    abstract protected function handle(Command $command): void;
}
