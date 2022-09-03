<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus\Tactician;

use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\CommandBus as TacticianBus;

final class TacticianSyncCommandBus implements CommandBus
{
    private $commandBus;

    public function __construct(CommandHandlerMiddleware $commandHandlerMiddleware)
    {
        $this->commandBus = new TacticianBus([$commandHandlerMiddleware]);
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->handle($command);
    }

}
