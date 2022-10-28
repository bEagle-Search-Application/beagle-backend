<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationTokenDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserVerificationTokenDataTransformer
{
    /**
     * @throws TokenExpired
     * @throws InvalidTokenSignature
     */
    public function fromDao(UserVerificationTokenDao $userVerificationDao): UserVerificationToken
    {
        return new UserVerificationToken(
            UserVerificationTokenId::fromString($userVerificationDao->getAttribute(UserVerificationTokenDao::ID)),
            UserId::fromString($userVerificationDao->getAttribute(UserVerificationTokenDao::USER_ID)),
            Token::accessTokenFromString($userVerificationDao->getAttribute(UserVerificationTokenDao::TOKEN)),
        );
    }
}
