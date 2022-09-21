<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Shared\Domain\ValueObjects\Email;

final class UserVerificationNotFound extends \Exception
{
    private const INVALID_EMAIL = "No se ha enviado ningún correo de validación a %s";

    public static function byEmail(Email $email):self
    {
        return new self(\sprintf(self::INVALID_EMAIL, $email->value()));
    }
}
