<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Tests\MotherObjects\PhoneMotherObject;
use Tests\MotherObjects\PhonePrefixMotherObject;

final class UserPhoneMotherObject
{
    /** @throws InvalidValueObject */
    public static function create(
        ?string $phonePrefix = null,
        ?string $phone = null
    ):UserPhone {
        return UserPhone::create(
            $phonePrefix ?? PhonePrefixMotherObject::create(),
            $phone ?? PhoneMotherObject::create(),
        );
    }
}
