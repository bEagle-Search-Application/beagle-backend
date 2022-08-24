<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;

final class InMemoryUserRepository implements UserRepository
{
    /** @var User[] */
    private array $users = [];

    public function save(User $user):void
    {
        $this->users[] = $user;
    }

    /** @throws UserNotFound */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User
    {
        foreach ($this->users as $user)
        {
            if ($user->email()->equals($userEmail) && $user->password()->equals($userPassword))
            {
                return $user;
            }
        }

        throw UserNotFound::byCredentials($userEmail);
    }
}
