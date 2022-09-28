<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Shared\Domain\ValueObjects\Guid;

final class IdMotherObject
{
    public static function create(?string $guid = null):Guid
    {
        return empty($guid)
            ? Guid::fromString(
                Guid::v4()->toBase58()
            )
            : Guid::fromString($guid);
    }
}
