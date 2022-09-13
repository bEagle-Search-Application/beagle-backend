<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;
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
            empty($phonePrefix) ? PhonePrefixMotherObject::create() : PhonePrefix::fromString($phonePrefix),
            empty($phone) ? PhoneMotherObject::create() : Phone::fromString($phonePrefix),
        );
    }
}
