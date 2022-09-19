<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidPhone extends InvalidValueObject
{
    private const INVALID_FORMAT = "El teléfono %s tiene un formato inválido";

    public static function byFormat(string $phone):self
    {
        return new self(\sprintf(self::INVALID_FORMAT, $phone));
    }
}
