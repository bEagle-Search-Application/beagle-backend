<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserToken;

final class UserTokenMotherObject
{
    public static function create(?string $userToken = null):UserToken
    {
        return empty($userToken)
            ? UserToken::fromString(UserToken::generateRandom64String()->value())
            : UserToken::fromString($userToken);
    }
}
