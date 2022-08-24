<?php

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;

interface UserRepository
{
    /**
     * @throws UserNotFound
     * @throws InvalidEmail
     * @throws InvalidPassword
     */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User;

    public function save(User $user):void;
}
