<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Auth;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\TokenType;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;

final class TokenServiceMock implements TokenService
{
    public function generateAccessToken(UserId $userId):Token
    {
        $token = $userId->value() . "." . DateTime::now()->timestamp;

        return Token::accessTokenFromString($token);
    }

    public function generateRefreshToken(UserId $userId):Token
    {
        $token = $userId->value() . "." . DateTime::now()->timestamp;

        return Token::refreshTokenFromString($token);
    }

    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes):Token
    {
        $token = $userId->value()
                 . "."
                 . DateTime::now()->addMinutes($minutes)->timestamp;

        return Token::accessTokenFromString($token);
    }

    public function userIdFromToken(Token $token):UserId
    {
        $userId = \explode(".", $token->value())[0];

        return UserId::fromString($userId);
    }

    public function validateSignature(Token $token):void
    {
        $signature = \explode(".", $token->value())[2];

        if ($token->isAnAccessToken() && $signature != TokenType::ACCESS->name) {
            throw InvalidToken::byAccessSignature();
        }

        if ($token->isARefreshToken() && $signature != TokenType::REFRESH->name) {
            throw InvalidToken::byRefreshSignature();
        }
    }

    public function validateExpiration(Token $token):void
    {
        $expirationTime = \explode(".", $token->value())[1];

        if (DateTime::now()->timestamp > $expirationTime) {
            throw TokenExpired::byExpirationDate();
        }
    }
}
