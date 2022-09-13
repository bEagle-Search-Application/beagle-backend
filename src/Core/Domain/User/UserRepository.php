<?php

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\Errors\UserNotFound;

interface UserRepository
{
    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User;

    /** @throws CannotSaveUser */
    public function save(User $user):void;

    /** @throws UserNotFound */
    public function findByToken(UserToken $token):bool;
}
