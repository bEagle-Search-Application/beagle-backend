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
use Beagle\Shared\Domain\ValueObjects\Token;

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
     * TODO: Refactor this method if you use on production
     * This method is only for testing
     *
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

    /**
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalRefreshToken
     * @throws PersonalRefreshTokenNotFound
     */
    public function findByUserIdAndToken(
        UserId $userId,
        Token $token
    ):PersonalRefreshToken {
        $personalAccessTokenDao = PersonalRefreshTokenDao::where([
            ['user_id', $userId->value()],
            ['token', $token->value()],
        ])->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalRefreshTokenNotFound::byUserIdAndToken($userId);
        }

        return $this->personalRefreshTokenDataTransformer->fromDao($personalAccessTokenDao);
    }
}
