<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus;

abstract class EventListener
{
    final public function __invoke(Event $event): void
    {
        $this->listen($event);
    }

    abstract protected function listen(Event $event): void;
}
