<?php declare(strict_types = 1);

namespace Tests\MotherObjects\PersonalToken;

use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;

final class PersonalTokenIdMotherObject
{
    public static function create(?string $guid = null):PersonalTokenId
    {
        return empty($guid)
            ? PersonalTokenId::fromString(
                PersonalTokenId::v4()->toBase58()
            )
            : PersonalTokenId::fromString($guid);
    }
}
