<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus\Tactician;

use Beagle\Shared\Bus\Event;
use Beagle\Shared\Bus\EventBus;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\CommandBus as TacticianBus;

final class TacticianSyncEventBus implements EventBus
{
    private $eventBus;

    public function __construct(CommandHandlerMiddleware $commandHandlerMiddleware)
    {
        $this->eventBus = new TacticianBus([$commandHandlerMiddleware]);
    }

    public function dispatch(Event ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->eventBus->handle($event);
            } catch (MissingHandlerException $exception) {
                // If there are no listeners for that event, move on
                continue;
            }
        }
    }
}
