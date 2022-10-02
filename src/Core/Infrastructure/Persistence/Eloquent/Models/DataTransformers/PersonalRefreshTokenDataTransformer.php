<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\PersonalRefreshTokenDao;
use Beagle\Shared\Domain\ValueObjects\Token;

final class PersonalRefreshTokenDataTransformer
{
    /** @throws InvalidPersonalRefreshToken */
    public function fromDao(PersonalRefreshTokenDao $accessTokenDao): PersonalRefreshToken
    {
        return new PersonalRefreshToken(
            PersonalTokenId::fromString($accessTokenDao->id),
            UserId::fromString($accessTokenDao->user_id),
            Token::refreshTokenFromString($accessTokenDao->token)
        );
    }
}