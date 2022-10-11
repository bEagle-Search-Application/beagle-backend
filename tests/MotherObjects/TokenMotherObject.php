<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class TokenMotherObject
{
    public static function createAccessToken(?UserId $userId = null):Token
    {
        return Token::createAccessToken($userId ?? UserIdMotherObject::create());
    }

    public static function createExpiredAccessToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        return Token::createAccessTokenWithCustomPayload([
            'iat' => DateTime::now(),
            'uid' => $userId,
            'exp' => DateTimeMotherObject::yesterday()->timestamp,
            'iss' => \env('APP_URL')
        ]);
    }

    public static function createRefreshToken(?UserId $userId = null):Token
    {
        return Token::createRefreshToken($userId ?? UserIdMotherObject::create());
    }

    public static function createExpiredRefreshToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        return Token::createRefreshTokenWithCustomPayload([
            'iat' => DateTime::now(),
            'uid' => $userId,
            'exp' => DateTimeMotherObject::yesterday()->timestamp,
        ]);
    }
}
