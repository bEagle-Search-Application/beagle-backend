<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\Event;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Shared\Bus\Event;

final class UserCreated implements Event
{
    public function __construct(private UserEmail $email)
    {
    }

    public function email():UserEmail
    {
        return $this->email;
    }
}
