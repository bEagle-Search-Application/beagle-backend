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
            ['user_id' => $personalRefreshToken->userId()->value()],
            [
                'id' => $personalRefreshToken->id()->value(),
                'token' => $personalRefreshToken->token()->value(),
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
        $personalAccessTokenDao = PersonalRefreshTokenDao::where('user_id', $userId->value())->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalRefreshTokenNotFound::byUserId($userId);
        }

        return $this->personalRefreshTokenDataTransformer->fromDao($personalAccessTokenDao);
    }

    public function deleteByUserId(UserId $userId):void
    {
        PersonalRefreshTokenDao::where('user_id', $userId->value())->delete();
    }
}
