<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;

final class UserEmailChangeVerification
{
    public function __construct(
        private UserId $userId,
        private UserEmail $oldEmail,
        private UserEmail $newEmail,
        private bool $confirmed
    ) {
    }

    public static function create(
        UserId $userId,
        UserEmail $oldEmail,
        UserEmail $newEmail,
    ):self {
        return new self(
            $userId,
            $oldEmail,
            $newEmail,
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

    public function confirmed():bool
    {
        return $this->confirmed;
    }

    public function confirm():void
    {
        $this->confirmed = true;
    }
}
