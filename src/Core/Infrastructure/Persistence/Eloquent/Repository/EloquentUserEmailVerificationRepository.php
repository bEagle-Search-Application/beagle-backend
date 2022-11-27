<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Domain\User\UserEmailVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserEmailVerificationDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserEmailVerificationDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;

final class EloquentUserEmailVerificationRepository implements UserEmailVerificationRepository
{
    public function __construct(private UserEmailVerificationDataTransformer $userEmailVerificationDataTransformer)
    {
    }

    public function save(UserEmailVerification $userEmailVerification):void
    {
        UserEmailVerificationDao::updateOrCreate(
            [UserEmailVerificationDao::USER_ID => $userEmailVerification->userId()->value()],
            [
                UserEmailVerificationDao::ID => $userEmailVerification->id()->value(),
                UserEmailVerificationDao::TOKEN => $userEmailVerification->token()->value(),
            ]
        );
    }

    /**
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     */
    public function findByUserId(UserId $userId):UserEmailVerification
    {
        $userVerificationDao = UserEmailVerificationDao::where(
            UserEmailVerificationDao::USER_ID,
            $userId->value()
        )->first();

        if ($userVerificationDao === null) {
            throw UserVerificationNotFound::byUserId($userId);
        }

        return $this->userEmailVerificationDataTransformer->fromDao($userVerificationDao);
    }

    /**
     * @throws TokenExpired
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     */
    public function find(UserEmailVerificationId $userVerificationTokenId):UserEmailVerification
    {
        $userVerificationDao = UserEmailVerificationDao::where(
            UserEmailVerificationDao::ID,
            $userVerificationTokenId->value()
        )->first();

        if ($userVerificationDao === null) {
            throw UserVerificationNotFound::byId($userVerificationTokenId);
        }

        return $this->userEmailVerificationDataTransformer->fromDao($userVerificationDao);
    }
}
