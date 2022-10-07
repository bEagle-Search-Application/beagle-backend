<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken\Errors;

final class InvalidPersonalRefreshToken extends \Exception
{
    private const INVALID_TYPE = "El tipo de token es inválido. Debe introducir un token de refresco";

    public static function byType():self
    {
        return new self(self::INVALID_TYPE);
    }
}
