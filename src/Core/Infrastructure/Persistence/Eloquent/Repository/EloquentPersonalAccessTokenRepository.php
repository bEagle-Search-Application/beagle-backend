<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\PersonalAccessTokenDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\PersonalAccessTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

final class EloquentPersonalAccessTokenRepository implements PersonalAccessTokenRepository
{
    public function __construct(private PersonalAccessTokenDataTransformer $personalAccessTokenDataTransformer)
    {
    }

    public function save(PersonalAccessToken $personalAccessToken):void
    {
        PersonalAccessTokenDao::updateOrCreate(
            [PersonalAccessTokenDao::USER_ID => $personalAccessToken->userId()->value()],
            [
                PersonalAccessTokenDao::ID => $personalAccessToken->id()->value(),
                PersonalAccessTokenDao::TOKEN => $personalAccessToken->token()->value(),
            ]
        );
    }

    /**
     * TODO: Refactor this method if you use on production
     * This method is only for testing
     *
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws PersonalAccessTokenNotFound
     */
    public function findByUserId(UserId $userId):PersonalAccessToken
    {
        $personalAccessTokenDao = PersonalAccessTokenDao::where(
            PersonalAccessTokenDao::USER_ID,
            $userId->value()
        )->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalAccessTokenNotFound::byUserId($userId);
        }

        return $this->personalAccessTokenDataTransformer->fromDao($personalAccessTokenDao);
    }

    public function deleteByUserId(UserId $userId):void
    {
        PersonalAccessTokenDao::where(PersonalAccessTokenDao::USER_ID, $userId->value())->delete();
    }

    /**
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws InvalidTokenSignature
     * @throws PersonalAccessTokenNotFound
     */
    public function findByUserIdAndToken(
        UserId $userId,
        Token $token
    ):PersonalAccessToken {
        $personalAccessTokenDao = PersonalAccessTokenDao::where([
            ['user_id', $userId->value()],
            ['token', $token->value()],
        ])->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalAccessTokenNotFound::byUserIdAndToken($userId);
        }

        return $this->personalAccessTokenDataTransformer->fromDao($personalAccessTokenDao);
    }
}
