<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;

interface UserEmailVerificationRepository
{
    public function save(UserEmailVerification $userEmailVerification):void;

    /**
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     */
    public function findByUserId(UserId $userId):UserEmailVerification;

    /**
     * @throws TokenExpired
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     */
    public function find(UserEmailVerificationId $userVerificationTokenId):UserEmailVerification;
}
