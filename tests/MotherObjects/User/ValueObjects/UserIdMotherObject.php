<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserIdMotherObject
{
    public static function create(?string $userId = null):UserId
    {
        return empty($userId)
            ? UserId::fromString(
                UserId::v4()->toBase58()
            )
            : UserId::fromString($userId);
    }
}
