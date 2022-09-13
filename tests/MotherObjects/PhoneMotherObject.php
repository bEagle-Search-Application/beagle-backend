<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Shared\Domain\Errors\InvalidPhone;
use Beagle\Shared\Domain\ValueObjects\Phone;

final class PhoneMotherObject
{
    /** @throws InvalidPhone */
    public static function create(?string $phone = null):Phone
    {
        return empty($phone)
            ? Phone::fromString(StringMotherObject::createPhone())
            : Phone::fromString($phone);
    }
}
