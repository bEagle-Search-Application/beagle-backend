<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailVerificationIdMotherObject;

final class UserEmailVerificationMotherObject
{
    public static function create(
        ?UserEmailVerificationId $id = null,
        ?UserId $userId = null,
        ?Token $token = null,
    ):UserEmailVerification {
        $userId = $userId ?? UserIdMotherObject::create();

        return new UserEmailVerification(
            $id ?? UserEmailVerificationIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createAccessToken($userId)
        );
    }

    public static function createExpiredAccessToken(
        ?UserEmailVerificationId $id = null,
        ?UserId $userId = null
    ):UserEmailVerification {
        $userId = $userId ?? UserIdMotherObject::create();

        return new UserEmailVerification(
            $id ?? UserEmailVerificationIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createExpiredAccessToken($userId)
        );
    }
}
