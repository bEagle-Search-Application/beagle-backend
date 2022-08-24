<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Symfony\Component\Uid\Uuid;

final class IdMotherObject
{
    public static function create(): Uuid
    {
        return Uuid::v4();
    }
}
