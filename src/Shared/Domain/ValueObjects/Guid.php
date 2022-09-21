<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Symfony\Component\Uid\Uuid;

class Guid extends Uuid
{
    public static function generate():self
    {
        return new self(Uuid::v4()->jsonSerialize());
    }

    public function value():string
    {
        return $this->toBase58();
    }
}
