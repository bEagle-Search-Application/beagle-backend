<?php

namespace Beagle\Shared\Bus;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
