<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Faker\Factory;

final class UserEmailMotherObject
{
    /** @throws InvalidEmail */
    public static function create(?string $userEmail = null):UserEmail
    {
        return UserEmail::fromString($userEmail ?? Factory::create()->email);
    }
}
