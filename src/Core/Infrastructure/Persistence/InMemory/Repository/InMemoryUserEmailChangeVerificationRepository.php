<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\InMemory\Repository;

use Beagle\Core\Domain\User\Errors\UserEmailChangeVerificationNotFound;
use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;

final class InMemoryUserEmailChangeVerificationRepository implements UserEmailChangeVerificationRepository
{
    /** @var UserEmailChangeVerification[]  */
    private array $userVerifications = [];

    public function save(UserEmailChangeVerification $userChangeEmailVerification):void
    {
        foreach ($this->userVerifications as $key => $userVerification) {
            if ($userVerification->userId()->equals($userChangeEmailVerification->userId())) {
                $this->userVerifications[$key] = $userChangeEmailVerification;
                return;
            }
        }

        $this->userVerifications[] = $userChangeEmailVerification;
    }

    /** @throws UserEmailChangeVerificationNotFound */
    public function find(UserId $userId): UserEmailChangeVerification
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->userId()->equals($userId)) {
                return $userVerification;
            }
        }

        throw UserEmailChangeVerificationNotFound::byUserId($userId);
    }
}
