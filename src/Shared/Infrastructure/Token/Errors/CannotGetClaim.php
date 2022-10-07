<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Token\Errors;

final class CannotGetClaim extends \Exception
{
    private const INVALID_CLAIM = "La propiedad %s no existe en el payload";

    public static function byKey(string $claim):self
    {
        return new self(\sprintf(self::INVALID_CLAIM, $claim));
    }
}
