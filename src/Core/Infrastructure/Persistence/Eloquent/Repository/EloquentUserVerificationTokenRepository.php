<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserVerificationDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationTokenDao;

final class EloquentUserVerificationTokenRepository implements UserVerificationTokenRepository
{
    public function __construct(private UserVerificationDataTransformer $userVerificationDataTransformer)
    {
    }

    public function save(UserVerificationToken $userVerificationToken):void
    {
        UserVerificationTokenDao::updateOrCreate(
            ['user_id' => $userVerificationToken->userId()->value()],
            [
                'id' => $userVerificationToken->id()->value(),
                'token' => $userVerificationToken->token()->value(),
            ]
        );
    }

    /** @throws UserVerificationNotFound */
    public function findByUserId(UserId $userId):UserVerificationToken
    {
        $userVerificationDao = UserVerificationTokenDao::where('user_id', $userId->value())->first();

        if ($userVerificationDao === null) {
            throw UserVerificationNotFound::byUserId($userId);
        }

        return $this->userVerificationDataTransformer->fromDao($userVerificationDao);
    }

    /** @throws UserVerificationNotFound */
    public function find(UserVerificationTokenId $userVerificationTokenId):UserVerificationToken
    {
        $userVerificationDao = UserVerificationTokenDao::where('id', $userVerificationTokenId->value())->first();

        if ($userVerificationDao === null) {
            dump("Dentro del error");
            throw UserVerificationNotFound::byId($userVerificationTokenId);
        }

        return $this->userVerificationDataTransformer->fromDao($userVerificationDao);
    }
}
