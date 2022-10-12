<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\Errors\InvalidPassword;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Tests\MotherObjects\IntegerMotherObject;

final class UserPasswordMotherObject
{
    /** @throws InvalidPassword */
    public static function create(?string $password = null):UserPassword
    {
        $hashedPassword = empty($password) ? \md5((string) IntegerMotherObject::createWithDigits(9)) : \md5($password);
        return UserPassword::fromString($hashedPassword);
    }
}
