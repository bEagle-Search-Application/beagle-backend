<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserNotFound extends \Exception
{
    private const INVALID_CREDENTIALS = "Las credenciales de %s son incorrectas";
    private const INVALID_EMAIL = "El usuario con email %s no existe";
    private const INVALID_ID = "El usuario con id %s no existe";

    public static function byCredentials(UserEmail $userEmail): self
    {
        return new self(\sprintf(self::INVALID_CREDENTIALS, $userEmail->value()));
    }

    public static function byEmail(UserEmail $email):self
    {
        return new self(\sprintf(self::INVALID_EMAIL, $email->value()));
    }

    public static function byId(UserId $userId):self
    {
        return new self(\sprintf(self::INVALID_ID, $userId->value()));
    }
}
