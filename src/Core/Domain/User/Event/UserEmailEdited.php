<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Event;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Event;

final class UserEmailEdited implements Event
{
    public function __construct(
        private UserId $id,
        private UserEmail $oldEmail,
        private UserEmail $newEmail
    ) {
    }

    public function id():UserId
    {
        return $this->id;
    }

    public function oldEmail():UserEmail
    {
        return $this->oldEmail;
    }

    public function newEmail():UserEmail
    {
        return $this->newEmail;
    }
}
