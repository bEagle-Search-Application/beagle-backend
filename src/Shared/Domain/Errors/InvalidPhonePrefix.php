<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidPhonePrefix extends InvalidValueObject
{
    private const INVALID_CODE = "The phone code %s is invalid";

    public static function byCode(string $prefixPhone):self
    {
        return new self(\sprintf(self::INVALID_CODE, $prefixPhone));
    }
}
