<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Symfony\Component\Uid\Uuid;

class Guid extends Uuid
{
    public function value():string
    {
        return $this->toBase58();
    }
}
