<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Shared\Domain\Errors\InvalidValueObject;

final class InvalidPassword extends InvalidValueObject
{
    private const INVALID_ENCRYPTION = "La codificación de la contraseña es inválida";

    public static function byEncryption():self
    {
        return new self(self::INVALID_ENCRYPTION);
    }
}
