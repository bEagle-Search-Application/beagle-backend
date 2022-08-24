<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;

final class UserNotFound extends \Exception
{
    private const INVALID_CREDENTIALS = "The credentials for %s are invalid";

    public static function byCredentials(UserEmail $userEmail): self
    {
        return new self(\sprintf(self::INVALID_CREDENTIALS, $userEmail->value()));
    }
}
