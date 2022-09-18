<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidPhone extends InvalidValueObject
{
    private const INVALID_FORMAT = "The number %s has an invalid format";

    public static function byFormat(string $phone):self
    {
        return new self(\sprintf(self::INVALID_FORMAT, $phone));
    }
}
