<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Faker\Factory;

final class UserEmailMotherObject
{
    public static function create(?string $userEmail = null):UserEmail
    {
        return empty($userEmail)
            ? UserEmail::fromString(Factory::create()->email)
            : UserEmail::fromString($userEmail);
    }
}
