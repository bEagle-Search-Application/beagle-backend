<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;

final class UserVerificationNotFound extends \Exception
{
    private const INVALID_USER_ID = "No se ha encontrado ninguna validación para el usuario %s";
    private const INVALID_ID = "No se ha encontrado ninguna validación para el id %s";

    public static function byUserId(UserId $userId):self
    {
        return new self(\sprintf(self::INVALID_USER_ID, $userId->value()));
    }

    public static function byId(UserVerificationTokenId $userVerificationTokenId):self
    {
        return new self(\sprintf(self::INVALID_ID, $userVerificationTokenId->value()));
    }
}
