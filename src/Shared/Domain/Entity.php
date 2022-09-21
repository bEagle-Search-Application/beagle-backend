<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain;

use Beagle\Shared\Bus\Event;

abstract class Entity
{
    /** @var Event[] */
    protected array $events = [];

    /** @return Event[] */
    final public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    final public function recordThat(Event $event): void
    {
        $this->events[] = $event;
    }

}
