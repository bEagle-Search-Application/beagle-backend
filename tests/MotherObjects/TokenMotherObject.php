<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use ReallySimpleJWT\Token as SimpleJwt;

final class TokenMotherObject
{
    public static function createAccessToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $userId,
                'exp' => DateTimeMotherObject::now()->addMinutes(10)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        return Token::accessTokenFromString($token);
    }

    public static function createExpiredAccessToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $userId,
                'exp' => DateTimeMotherObject::yesterday()->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        return Token::accessTokenFromString($token);
    }

    public static function createRefreshToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $userId,
                'exp' => DateTimeMotherObject::now()->addDays(15)->timestamp,
            ],
            \env('JWT_REFRESH_SECRET')
        );
        return Token::refreshTokenFromString($token);
    }

    public static function createExpiredRefreshToken(?UserId $userId = null):Token
    {
        $userId = empty($userId) ? UserIdMotherObject::create()->value() : $userId->value();

        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $userId,
                'exp' => DateTimeMotherObject::yesterday()->timestamp,
            ],
            \env('JWT_REFRESH_SECRET')
        );
        return Token::refreshTokenFromString($token);
    }
}
