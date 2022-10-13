<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Http\Errors;

final class HeaderNotFound extends \Exception
{
    private const INVALID_VALUE = "El header %s no está presente en la solicitud";

    public static function byValue(string $header):self
    {
        return new self(\sprintf(self::INVALID_VALUE, $header));
    }
}
