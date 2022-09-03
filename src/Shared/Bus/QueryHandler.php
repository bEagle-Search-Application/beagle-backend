<?php declare(strict_types = 1);

namespace Beagle\Shared\Bus;

abstract class QueryHandler
{
    final public function __invoke(Query $query): QueryResponse
    {
        return $this->handle($query);
    }

    abstract protected function handle(Query $query): QueryResponse;
}
