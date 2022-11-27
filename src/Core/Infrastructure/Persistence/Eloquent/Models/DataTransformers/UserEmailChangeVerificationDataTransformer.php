<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserEmailChangeVerificationDao;
use Beagle\Shared\Domain\Errors\InvalidEmail;

final class UserEmailChangeVerificationDataTransformer
{
    /** @throws InvalidEmail */
    public function fromDao(UserEmailChangeVerificationDao $userEmailChangeVerificationDao): UserEmailChangeVerification
    {
        return new UserEmailChangeVerification(
            UserId::fromString($userEmailChangeVerificationDao->getAttribute(UserEmailChangeVerificationDao::USER_ID)),
            UserEmail::fromString(
                $userEmailChangeVerificationDao->getAttribute(UserEmailChangeVerificationDao::OLD_EMAIL)
            ),
            UserEmail::fromString(
                $userEmailChangeVerificationDao->getAttribute(UserEmailChangeVerificationDao::NEW_EMAIL)
            ),
            (bool) $userEmailChangeVerificationDao->getAttribute(UserEmailChangeVerificationDao::CONFIRMED)
        );
    }
}
