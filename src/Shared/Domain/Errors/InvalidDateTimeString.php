<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidDateTimeString extends InvalidValueObject
{
    private const EXCEPTION_MESSAGE = '%s no es un formato válido';

    public static function invalidValue(string $value): self
    {
        return new self(sprintf(self::EXCEPTION_MESSAGE, $value));
    }
}
