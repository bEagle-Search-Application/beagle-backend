<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Shared\Domain\TokenType;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\DateTimeMotherObject;
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
        $userId = empty($userId) ? UserIdMotherObject::create() : $userId;

        return new UserVerificationToken(
            empty($id) ? UserVerificationTokenIdMotherObject::create() : $id,
            $userId,
            empty($token)
                ? TokenMotherObject::customize(
                    TokenType::ACCESS,
                    $userId,
                    DateTimeMotherObject::inFuture(10)
                )
                : $token,
        );
    }

    public static function createExpiredAccessToken(
        ?UserVerificationTokenId $id = null,
        ?UserId $userId = null
    ):UserVerificationToken {
        $userId = empty($userId) ? UserIdMotherObject::create() : $userId;

        return new UserVerificationToken(
            empty($id) ? UserVerificationTokenIdMotherObject::create() : $id,
            $userId,
            TokenMotherObject::customize(
                TokenType::ACCESS,
                $userId,
                DateTimeMotherObject::yesterday()
            )
        );
    }
}
