<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\PersonalRefreshTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

final class PersonalRefreshTokenDataTransformer
{
    /**
     * @throws TokenExpired
     * @throws InvalidPersonalRefreshToken
     * @throws InvalidTokenSignature
     */
    public function fromDao(PersonalRefreshTokenDao $accessTokenDao): PersonalRefreshToken
    {
        return new PersonalRefreshToken(
            PersonalTokenId::fromString($accessTokenDao->getAttribute(PersonalRefreshTokenDao::ID)),
            UserId::fromString($accessTokenDao->getAttribute(PersonalRefreshTokenDao::USER_ID)),
            Token::refreshTokenFromString($accessTokenDao->getAttribute(PersonalRefreshTokenDao::TOKEN))
        );
    }
}
