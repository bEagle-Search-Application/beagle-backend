<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\UserEmailChangeVerificationNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidEmail;

interface UserEmailChangeVerificationRepository
{
    public function save(UserEmailChangeVerification $userChangeEmailVerification):void;

    /**
     * @throws InvalidEmail
     * @throws UserEmailChangeVerificationNotFound
     */
    public function find(UserId $userId):UserEmailChangeVerification;
}
