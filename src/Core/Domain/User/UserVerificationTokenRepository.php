<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;

interface UserVerificationTokenRepository
{
    public function save(UserVerificationToken $userVerificationToken):void;

    /** @throws UserVerificationNotFound */
    public function findByUserId(UserId $userId):UserVerificationToken;

    /** @throws UserVerificationNotFound */
    public function find(UserVerificationTokenId $userVerificationTokenId):UserVerificationToken;
}
