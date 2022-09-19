<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;

final class CannotSaveUser extends \Exception
{
    private const DUPLICATE_EMAIL = "El email %s ya existe";

    public static function byEmail(UserEmail $email):self
    {
        return new self(\sprintf(self::DUPLICATE_EMAIL, $email->value()));
    }
}
