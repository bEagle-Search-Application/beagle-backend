<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserId;

final class PersonalAccessTokenNotFound extends \Exception
{
    private const INVALID_USER_ID = "No se ha encontrado ningún token de acceso asociado al usuario %s";

    public static function byUserId(UserId $userId):self
    {
        return new self(\sprintf(self::INVALID_USER_ID, $userId->value()));
    }
}
