<?php

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Domain\Errors\InvalidValueObject;

interface UserRepository
{
    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User;

    /** @throws CannotSaveUser */
    public function save(User $user):void;

    /**
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    public function findByEmail(UserEmail $email):User;

    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function find(UserId $userId):User;
}
