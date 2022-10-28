<?php declare(strict_types = 1);

namespace Tests\MotherObjects\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class PersonalTokenMotherObject
{
    /** @throws InvalidPersonalAccessToken */
    public static function createPersonalAccessToken(
        ?PersonalTokenId $id = null,
        ?UserId $userId = null,
        ?Token $token = null,
    ):PersonalAccessToken {
        $userId = $userId ?? UserIdMotherObject::create();

        return new PersonalAccessToken(
            $id ?? PersonalTokenIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createAccessToken($userId)
        );
    }

    /** @throws InvalidPersonalRefreshToken */
    public static function createPersonalRefreshToken(
        ?PersonalTokenId $id = null,
        ?UserId $userId = null,
        ?Token $token = null,
    ):PersonalRefreshToken {
        $userId = $userId ?? UserIdMotherObject::create();

        return new PersonalRefreshToken(
            $id ?? PersonalTokenIdMotherObject::create(),
            $userId,
            $token ?? TokenMotherObject::createRefreshToken($userId)
        );
    }
}
