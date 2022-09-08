<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Faker\Factory;
use Tests\MotherObjects\IdMotherObject;

final class UserIdMotherObject
{
    public static function create(?string $userId = null):UserId
    {
        return empty($userId)
            ? UserId::fromString(
                IdMotherObject::create()->toBase58()
            )
            : UserId::fromString($userId);
    }
}
