<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\InMemory\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Domain\User\UserEmailVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;

final class InMemoryUserEmailVerificationRepository implements UserEmailVerificationRepository
{
    /** @var UserEmailVerification[]  */
    private array $userVerifications = [];

    public function save(UserEmailVerification $userEmailVerification):void
    {
        foreach ($this->userVerifications as $key => $userVerification) {
            if ($userVerification->userId()->equals($userEmailVerification->userId())) {
                $this->userVerifications[$key] = $userEmailVerification;
                return;
            }
        }

        $this->userVerifications[] = $userEmailVerification;
    }

    /** @throws UserVerificationNotFound */
    public function findByUserId(UserId $userId):UserEmailVerification
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->userId()->equals($userId)) {
                return $userVerification;
            }
        }

        throw UserVerificationNotFound::byUserId($userId);
    }

    public function find(UserEmailVerificationId $userVerificationTokenId):UserEmailVerification
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->id()->equals($userVerificationTokenId)) {
                return $userVerification;
            }
        }

        throw UserVerificationNotFound::byId($userVerificationTokenId);
    }
}
