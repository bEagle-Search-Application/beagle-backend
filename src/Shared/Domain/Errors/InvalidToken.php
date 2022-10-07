<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidToken extends InvalidValueObject
{
    private const INVALID_ACCESS_SIGNATURE = "La firma del token de acceso es inválida";
    private const INVALID_REFRESH_SIGNATURE = "La firma del token de refresco es inválida";

    public static function byAccessSignature():self
    {
        return new self(self::INVALID_ACCESS_SIGNATURE);
    }

    public static function byRefreshSignature():self
    {
        return new self(self::INVALID_REFRESH_SIGNATURE);
    }
}
