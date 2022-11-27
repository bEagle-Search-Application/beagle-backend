<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class UserEmailChangeVerificationMotherObject
{
    public static function create(
        ?UserId $userId = null,
        UserEmail $oldEmail = null,
        UserEmail $newEmail = null,
    ):UserEmailChangeVerification {
        return new UserEmailChangeVerification(
            $userId ?? UserIdMotherObject::create(),
            $oldEmail ?? UserEmailMotherObject::create(),
            $newEmail ?? UserEmailMotherObject::create(),
            false
        );
    }
}
