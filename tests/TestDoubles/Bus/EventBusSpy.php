<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Bus;

use Beagle\Shared\Bus\Event;
use Beagle\Shared\Bus\EventBus;

final class EventBusSpy implements EventBus
{
    /** @var array | Event[] */
    protected $dispatchedEvents = [];

    public function dispatch(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->dispatchedEvents[] = $event;
        }
    }

    public function eventDispatched(string $eventClass): bool
    {
        foreach ($this->dispatchedEvents as $dispatchedEvent) {
            if (\get_class($dispatchedEvent) === $eventClass) {
                return true;
            }
        }

        return false;
    }

    public function getDispatchedEventByType(string $eventClass): array
    {
        $result = [];
        foreach ($this->dispatchedEvents as $dispatchedEvent) {
            if (\get_class($dispatchedEvent) === $eventClass) {
                $result[] = $dispatchedEvent;
            }
        }
        return $result;
    }

    public function getLastDispatchedEvent(): ?Event
    {
        return $this->dispatchedEvents[\count($this->dispatchedEvents) - 1] ?? null;
    }

}
