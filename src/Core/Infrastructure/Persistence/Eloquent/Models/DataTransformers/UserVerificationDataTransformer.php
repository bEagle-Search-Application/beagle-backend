<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationTokenDao;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserVerificationDataTransformer
{
    public function fromDao(UserVerificationTokenDao $userVerificationDao): UserVerificationToken
    {
        return new UserVerificationToken(
            UserVerificationTokenId::fromString($userVerificationDao->id),
            UserId::fromString($userVerificationDao->user_id),
            Token::accessTokenFromString($userVerificationDao->token),
        );
    }
}
