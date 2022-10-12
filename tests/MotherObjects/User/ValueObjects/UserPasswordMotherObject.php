<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Faker\Factory;

final class UserPasswordMotherObject
{
    /** @throws InvalidPassword */
    public static function create(?string $password = null):UserPassword
    {
        return UserPassword::fromString($password ?? self::createNumberAsString());
    }

    /** @throws InvalidPassword */
    public static function createWithHash(?string $password = null):UserPassword
    {
        return UserPassword::fromString($password ?? \md5(self::createNumberAsString()));
    }

    private static function createNumberAsString():string
    {
        return (string) Factory::create()->randomNumber(9);
    }
}
