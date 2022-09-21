<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Shared\Domain\ValueObjects\Email;

final class InMemoryUserVerificationRepository implements UserVerificationRepository
{
    /** @var UserVerification[]  */
    private array $userVerifications = [];

    public function save(UserVerification $userVerification):void
    {
        $this->userVerifications[] = $userVerification;
    }

    /** @throws UserVerificationNotFound */
    public function findByEmail(Email $email):UserVerification
    {
        foreach ($this->userVerifications as $userVerification) {
            if ($userVerification->email()->equals($email)) {
               return $userVerification;
            }
        }

        throw UserVerificationNotFound::byEmail($email);
    }
}
