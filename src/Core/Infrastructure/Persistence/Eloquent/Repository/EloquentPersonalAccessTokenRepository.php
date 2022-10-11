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

final class EloquentPersonalAccessTokenRepository implements PersonalAccessTokenRepository
{
    public function __construct(private PersonalAccessTokenDataTransformer $personalAccessTokenDataTransformer)
    {
    }

    public function save(PersonalAccessToken $personalAccessToken):void
    {
        PersonalAccessTokenDao::updateOrCreate(
            ['user_id' => $personalAccessToken->userId()->value()],
            [
                'id' => $personalAccessToken->id()->value(),
                'token' => $personalAccessToken->token()->value(),
            ]
        );
    }

    /**
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws PersonalAccessTokenNotFound
     */
    public function findByUserId(UserId $userId):PersonalAccessToken
    {
        $personalAccessTokenDao = PersonalAccessTokenDao::where('user_id', $userId->value())->first();

        if ($personalAccessTokenDao === null) {
            throw PersonalAccessTokenNotFound::byUserId($userId);
        }

        return $this->personalAccessTokenDataTransformer->fromDao($personalAccessTokenDao);
    }

    public function deleteByUserId(UserId $userId):void
    {
        PersonalAccessTokenDao::where('user_id', $userId->value())->delete();
    }
}
