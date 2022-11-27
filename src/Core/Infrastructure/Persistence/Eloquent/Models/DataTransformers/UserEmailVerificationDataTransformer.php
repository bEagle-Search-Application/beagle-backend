<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserEmailVerificationDao;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserEmailVerificationDataTransformer
{
    /**
     * @throws TokenExpired
     * @throws InvalidTokenSignature
     */
    public function fromDao(UserEmailVerificationDao $userVerificationDao): UserEmailVerification
    {
        return new UserEmailVerification(
            UserEmailVerificationId::fromString($userVerificationDao->getAttribute(UserEmailVerificationDao::ID)),
            UserId::fromString($userVerificationDao->getAttribute(UserEmailVerificationDao::USER_ID)),
            Token::accessTokenFromString($userVerificationDao->getAttribute(UserEmailVerificationDao::TOKEN)),
        );
    }
}
