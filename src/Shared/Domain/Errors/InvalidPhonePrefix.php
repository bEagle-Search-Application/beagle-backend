<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidPhonePrefix extends InvalidValueObject
{
    private const INVALID_CODE = "El prefijo telefónico %s es inválido";

    public static function byCode(string $prefixPhone):self
    {
        return new self(\sprintf(self::INVALID_CODE, $prefixPhone));
    }
}
