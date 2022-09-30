<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Token;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

final class JwtTokenService implements TokenService
{
    const USER_ID_KEY = 'uid';

    public function generateAccessToken(UserId $userId):Token
    {
        $token = Token::customPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addMinutes(10)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        return Token::accessTokenFromString($token);
    }

    public function generateRefreshToken(UserId $userId):Token
    {
        $token = Token::customPayload(
            [
                'iat' => DateTime::now(),
                'exp' => DateTime::now()->addDays(15)->timestamp,
            ],
            \env('JWT_REFRESH_SECRET')
        );

        return Token::refreshTokenFromString($token);
    }

    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes):Token
    {
        $token = Token::customPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addMinutes($minutes)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        return Token::accessTokenFromString($token);
    }

    /** @throws CannotGetClaim */
    public function userIdFromToken(Token $token):UserId
    {
        $payload = Token::getPayload($token->value());
        $userId = $payload[self::USER_ID_KEY];

        if (!isset($userId)) {
            throw CannotGetClaim::byKey(self::USER_ID_KEY);
        }

        return UserId::fromString($userId);
    }

    /** @throws InvalidToken */
    public function validateSignature(Token $token):void
    {
        if ($token->isAnAccessToken() && !Token::validate($token->value(), \env('JWT_ACCESS_SECRET'))) {
            throw InvalidToken::byAccessSignature();
        }

        if ($token->isARefreshToken() && !Token::validate($token->value(), \env('JWT_REFRESH_SECRET'))) {
            throw InvalidToken::byRefreshSignature();
        }
    }

    /** @throws TokenExpired */
    public function validateExpiration(Token $token):void
    {
        if (!Token::validateExpiration($token->value())) {
            throw TokenExpired::byExpirationDate();
        }
    }
}
