<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Domain\ValueObjects\Email;

interface UserVerificationRepository
{
    public function save(UserVerification $userVerification):void;

    /** @throws UserVerificationNotFound */
    public function findByEmail(Email $email):UserVerification;

    /** @throws UserVerificationNotFound */
    public function findByToken(UserToken $userToken):UserVerification;
}
