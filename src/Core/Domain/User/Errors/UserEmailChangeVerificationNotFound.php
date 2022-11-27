<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserEmailChangeVerificationNotFound extends \Exception
{
    private const INVALID_USER_ID = "No se ha encontrado ninguna solicitud de cambio de email para el usuario %s";

    public static function byUserId(UserId $userId):self
    {
        return new self(\sprintf(self::INVALID_USER_ID, $userId->value()));
    }
}
