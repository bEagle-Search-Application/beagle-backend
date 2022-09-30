<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;

final class InMemoryUserVerificationTokenRepository implements UserVerificationTokenRepository
{
    /** @var UserVerificationToken[]  */
    private array $userVerifications = [];

    public function save(UserVerificationToken $userVerificationToken):void
    {
        $this->userVerifications[] = $userVerificationToken;
    }

    /** @throws UserVerificationNotFound */
    public function findByUserId(UserId $userId):UserVerificationToken
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->userId()->equals($userId)) {
                return $userVerification;
            }
        }

        throw UserVerificationNotFound::byUserId($userId);
    }

    public function find(UserVerificationTokenId $userVerificationTokenId):UserVerificationToken
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->id()->equals($userVerificationTokenId)) {
                return $userVerification;
            }
        }

        throw UserVerificationNotFound::byId($userVerificationTokenId);
    }
}
