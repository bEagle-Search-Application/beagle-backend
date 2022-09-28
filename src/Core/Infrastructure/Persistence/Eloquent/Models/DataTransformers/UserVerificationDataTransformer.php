<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationDao;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Email;
use Beagle\Shared\Domain\ValueObjects\Guid;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserVerificationDataTransformer
{
    public function fromDao(UserVerificationDao $userVerificationDao): UserVerification
    {
        return new UserVerification(
            Guid::fromString($userVerificationDao->id),
            Email::fromString($userVerificationDao->email),
            Token::fromString($userVerificationDao->token),
            DateTime::createFromString($userVerificationDao->expired_at),
        );
    }
}
