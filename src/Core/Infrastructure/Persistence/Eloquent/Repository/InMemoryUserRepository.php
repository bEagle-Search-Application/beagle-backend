<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserToken;

final class InMemoryUserRepository implements UserRepository
{
    /** @var User[] */
    private array $users = [];

    public function save(User $user):void
    {
        $this->ensureIfEmailAlreadyExists($user);
        $this->users[] = $user;
    }

    /** @throws UserNotFound */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User
    {
        foreach ($this->users as $user) {
            if ($user->email()->equals($userEmail) && $user->password()->equals($userPassword)) {
                return $user;
            }
        }

        throw UserNotFound::byCredentials($userEmail);
    }

    /** @throws CannotSaveUser */
    private function ensureIfEmailAlreadyExists(User $user)
    {
        foreach ($this->users as $registeredUser) {
            if ($this->emailBelongsToAnotherUser($registeredUser, $user)) {
                throw CannotSaveUser::byEmail($user->email());
            }
        }
    }

    public function findByToken(UserToken $token):bool
    {
        return true;
    }

    private function emailBelongsToAnotherUser(User $registeredUser, User $user):bool
    {
        return $registeredUser->email()->equals($user->email()) && !$registeredUser->id()->equals($user->id());
    }

    public function findByEmail(UserEmail $email):User
    {
        foreach ($this->users as $user) {
            if ($user->email()->equals($email)) {
                return $user;
            }
        }

        throw UserNotFound::byEmail($email);
    }
}
