<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\PersonalAccessTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

final class PersonalAccessTokenDataTransformer
{
    /**
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws InvalidTokenSignature
     */
    public function fromDao(PersonalAccessTokenDao $accessTokenDao): PersonalAccessToken
    {
        return new PersonalAccessToken(
            PersonalTokenId::fromString($accessTokenDao->id),
            UserId::fromString($accessTokenDao->user_id),
            Token::accessTokenFromString($accessTokenDao->token)
        );
    }
}
