<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;

final class UserNotFound extends \Exception
{
    private const INVALID_CREDENTIALS = "The credentials for %s are invalid";
    private const INVALID_TOKEN = "The token is invalid";

    public static function byCredentials(UserEmail $userEmail): self
    {
        return new self(\sprintf(self::INVALID_CREDENTIALS, $userEmail->value()));
    }

    public static function byToken(): self
    {
        return new self(self::INVALID_TOKEN);
    }
}
