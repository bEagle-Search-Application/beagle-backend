<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserCannotBeEdited extends \Exception
{
    private const USER_CANNOT_EDIT = "The user %s cannot edit this user information";

    public static function byUser(UserId $authorId):self
    {
        return new self(\sprintf(self::USER_CANNOT_EDIT, $authorId->value()));
    }
}
