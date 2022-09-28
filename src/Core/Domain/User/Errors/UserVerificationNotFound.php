<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Domain\ValueObjects\Email;

final class UserVerificationNotFound extends \Exception
{
    private const INVALID_EMAIL = "No se ha encontrado ninguna validación para el email %s";
    private const INVALID_TOKEN = "No se ha encontrado ninguna validación para el token %s";

    public static function byEmail(Email $email):self
    {
        return new self(\sprintf(self::INVALID_EMAIL, $email->value()));
    }

    public static function byToken(UserToken $userToken):self
    {
        return new self(\sprintf(self::INVALID_TOKEN, $userToken->value()));
    }
}
