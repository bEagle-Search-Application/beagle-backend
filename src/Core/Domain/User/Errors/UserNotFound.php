<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;

final class UserNotFound extends \Exception
{
    private const INVALID_CREDENTIALS = "Las credenciales de %s son incorrectas";
    private const INVALID_TOKEN = "El token es inválido";
    private const INVALID_EMAIL = "El email %s no existe";

    public static function byCredentials(UserEmail $userEmail): self
    {
        return new self(\sprintf(self::INVALID_CREDENTIALS, $userEmail->value()));
    }

    public static function byToken(): self
    {
        return new self(self::INVALID_TOKEN);
    }

    public static function byEmail(UserEmail $email):self
    {
        return new self(\sprintf(self::INVALID_EMAIL, $email->value()));
    }
}
