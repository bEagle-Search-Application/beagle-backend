<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Faker\Factory;

final class UserPasswordMotherObject
{
    public static function create(?string $password = null):UserPassword
    {
        return UserPassword::fromString($password ?? self::createNumberAsString());
    }

    public static function createWithHash(?string $password = null):UserPassword
    {
        return UserPassword::fromString($password ?? \md5(self::createNumberAsString()));
    }

    private static function createNumberAsString():string
    {
        return (string) Factory::create()->randomNumber(9);
    }
}
