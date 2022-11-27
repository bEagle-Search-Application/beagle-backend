<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserEmailChangeCannotBeValidated extends \Exception
{
    private const USER_EMAIL_CHANGE_CANNOT_BE_VALIDATE =
        "El usuario %s no puede validar esta confirmación de cambio de email";

    public static function byUser(UserId $authorId):self
    {
        return new self(\sprintf(self::USER_EMAIL_CHANGE_CANNOT_BE_VALIDATE, $authorId->value()));
    }
}
