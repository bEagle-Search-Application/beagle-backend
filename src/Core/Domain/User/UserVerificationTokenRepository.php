<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;

interface UserVerificationTokenRepository
{
    public function save(UserVerificationToken $userVerificationToken):void;

    /**
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     */
    public function findByUserId(UserId $userId):UserVerificationToken;

    /**
     * @throws TokenExpired
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     */
    public function find(UserVerificationTokenId $userVerificationTokenId):UserVerificationToken;
}
