<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus\Tactician;

use Beagle\Shared\Bus\Query;
use Beagle\Shared\Bus\QueryBus;
use Beagle\Shared\Bus\QueryResponse;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\CommandBus as TacticianBus;

final class TacticianSyncQueryBus implements QueryBus
{
    private $queryBus;

    public function __construct(CommandHandlerMiddleware $commandHandlerMiddleware)
    {
        $this->queryBus = new TacticianBus([$commandHandlerMiddleware]);
    }

    public function dispatch(Query $query): QueryResponse
    {
        return $this->queryBus->handle($query);
    }

}
