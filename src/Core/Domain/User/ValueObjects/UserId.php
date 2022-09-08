<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

use Symfony\Component\Uid\Uuid;

final class UserId extends Uuid
{
    public function value():string
    {
        return $this->toBase58();
    }
}
