<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserVerificationTokenDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;

final class EloquentUserVerificationTokenRepository implements UserVerificationTokenRepository
{
    public function __construct(private UserVerificationTokenDataTransformer $userVerificationDataTransformer)
    {
    }

    public function save(UserVerificationToken $userVerificationToken):void
    {
        UserVerificationTokenDao::updateOrCreate(
            [UserVerificationTokenDao::USER_ID => $userVerificationToken->userId()->value()],
            [
                UserVerificationTokenDao::ID => $userVerificationToken->id()->value(),
                UserVerificationTokenDao::TOKEN => $userVerificationToken->token()->value(),
            ]
        );
    }

    /**
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     */
    public function findByUserId(UserId $userId):UserVerificationToken
    {
        $userVerificationDao = UserVerificationTokenDao::where(
            UserVerificationTokenDao::USER_ID,
            $userId->value()
        )->first();

        if ($userVerificationDao === null) {
            throw UserVerificationNotFound::byUserId($userId);
        }

        return $this->userVerificationDataTransformer->fromDao($userVerificationDao);
    }

    /**
     * @throws TokenExpired
     * @throws UserVerificationNotFound
     * @throws InvalidTokenSignature
     */
    public function find(UserVerificationTokenId $userVerificationTokenId):UserVerificationToken
    {
        $userVerificationDao = UserVerificationTokenDao::where(
            UserVerificationTokenDao::ID,
            $userVerificationTokenId->value()
        )->first();

        if ($userVerificationDao === null) {
            throw UserVerificationNotFound::byId($userVerificationTokenId);
        }

        return $this->userVerificationDataTransformer->fromDao($userVerificationDao);
    }
}
