<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\TokenType;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class TokenMotherObject
{
    public static function createAccessToken(?string $token = null):Token
    {
        return empty($token)
            ? Token::accessTokenFromString(
                UserIdMotherObject::create()->value()
                . "."
                . DateTime::now()->addMinutes(10)->timestamp
            )
            : Token::accessTokenFromString($token);
    }

    public static function createRefreshToken(?string $token = null):Token
    {
        return empty($token)
            ? Token::refreshTokenFromString(
                UserIdMotherObject::create()->value()
                . "."
                . DateTime::now()->addMinutes(10)->timestamp
            )
            : Token::refreshTokenFromString($token);
    }

    public static function customize(
        TokenType $type,
        UserId $userId,
        DateTime $time
    ):Token {
        return ($type === TokenType::ACCESS)
            ? Token::accessTokenFromString($userId->value(). "." . $time->timestamp)
            : Token::refreshTokenFromString($userId->value() . "." . $time->timestamp);
    }
}
