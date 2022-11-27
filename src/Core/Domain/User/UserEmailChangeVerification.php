<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserEmailChangeVerification
{
    public function __construct(
        private UserId $userId,
        private UserEmail $oldEmail,
        private UserEmail $newEmail,
        private Token $token,
        private bool $confirmed
    ) {
    }

    public static function create(
        UserId $userId,
        UserEmail $oldEmail,
        UserEmail $newEmail,
        Token $token
    ):self {
        return new self(
            $userId,
            $oldEmail,
            $newEmail,
            $token,
            false
        );
    }

    public function userId():UserId
    {
        return $this->userId;
    }

    public function oldEmail():UserEmail
    {
        return $this->oldEmail;
    }

    public function newEmail():UserEmail
    {
        return $this->newEmail;
    }

    public function token():Token
    {
        return $this->token;
    }

    public function confirmed():bool
    {
        return $this->confirmed;
    }
}
