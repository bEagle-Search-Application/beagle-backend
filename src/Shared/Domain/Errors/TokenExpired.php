<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class TokenExpired extends \Exception
{
    private const EXPIRED_TOKEN = "El token ha caducado";

    public static function byExpirationDate():self
    {
        return new self(self::EXPIRED_TOKEN);
    }
}
