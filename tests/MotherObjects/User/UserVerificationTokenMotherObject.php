<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserVerificationTokenIdMotherObject;

final class UserVerificationTokenMotherObject
{
    public static function create(
        ?UserVerificationTokenId $id = null,
        ?UserId $userId = null,
        ?Token $token = null,
    ):UserVerificationToken {
        $userId = $userId ?? UserIdMotherObject::create();

        return new UserVerificationToken(
            $id ?? UserVerificationTokenIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createAccessToken($userId)
        );
    }

    public static function createExpiredAccessToken(
        ?UserVerificationTokenId $id = null,
        ?UserId $userId = null
    ):UserVerificationToken {
        $userId = $userId ?? UserIdMotherObject::create();

        return new UserVerificationToken(
            $id ?? UserVerificationTokenIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createExpiredAccessToken($userId)
        );
    }
}
