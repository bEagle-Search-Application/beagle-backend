<?php

namespace Beagle\Shared\Bus;

interface EventBus
{
    public function dispatch(Event ...$events): void;
}
