<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Faker\Factory;

final class UserPasswordMotherObject
{
    public static function create(?string $password = null):UserPassword
    {
        return empty($password)
            ? UserPassword::fromString(self::createStringNumber())
            : UserPassword::fromString($password);
    }

    public static function createWithHash(?string $password = null):UserPassword
    {
        return empty($password)
            ? UserPassword::fromString(\md5(self::createStringNumber()))
            : UserPassword::fromString($password);
    }

    private static function createStringNumber():string
    {
        return (string) Factory::create()->randomNumber(9);
    }
}
