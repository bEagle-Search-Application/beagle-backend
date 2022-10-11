<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

use Beagle\Shared\Domain\TokenType;

final class InvalidTokenSignature extends InvalidValueObject
{
    private const INVALID_ACCESS_SIGNATURE = "La firma del token no es del tipo %s";

    public static function byType(TokenType $tokenType):self
    {
        return new self(\sprintf(self::INVALID_ACCESS_SIGNATURE, $tokenType->name));
    }
}
