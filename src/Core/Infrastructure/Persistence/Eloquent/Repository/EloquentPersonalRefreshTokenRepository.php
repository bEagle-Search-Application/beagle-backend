<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\PersonalRefreshTokenDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\PersonalRefreshTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;

final class EloquentPersonalRefreshTokenRepository implements PersonalRefreshTokenRepository
{
    public function __construct(private PersonalRefreshTokenDataTransformer $personalRefreshTokenDataTransformer)
    {
    }

    public function save(PersonalRefreshToken $personalRefreshToken):void
    {
        PersonalRefreshTokenDao::updateOrCreate(
            [PersonalRefreshTokenDao::USER_ID => $personalRefreshToken->userId()->value()],
            [
                PersonalRefreshTokenDao::ID => $personalRefreshToken->id()->value(),
                PersonalRefreshTokenDao::TOKEN => $personalRefreshToken->token()->value(),
            ]
        );
    }

    /**
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalRefreshToken
     * @throws PersonalRefreshTokenNotFound
     */
    public function findByUserId(UserId $userId):PersonalRefreshToken
    {
        $personalAccessTokenDao = PersonalRefreshTokenDao::where(
            PersonalRefreshTokenDao::USER_ID,
            $userId->value()
        )->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalRefreshTokenNotFound::byUserId($userId);
        }

        return $this->personalRefreshTokenDataTransformer->fromDao($personalAccessTokenDao);
    }

    public function deleteByUserId(UserId $userId):void
    {
        PersonalRefreshTokenDao::where(PersonalRefreshTokenDao::USER_ID, $userId->value())->delete();
    }
}
