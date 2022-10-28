<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class PayloadValueNotFound extends \Exception
{
    private const INVALID_KEY = "No se ha podido encontrar la key '%s' en el payload del token";

    public static function byKey(string $key):self
    {
        return new self(\sprintf(self::INVALID_KEY, $key));
    }
}
