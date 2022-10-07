<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Token;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use ReallySimpleJWT\Token as SimpleJwt;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

final class JwtTokenService implements TokenService
{
    private const USER_ID_KEY = 'uid';

    public function generateAccessToken(UserId $userId):Token
    {
        $token = SimpleJwt::customPayload(
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
        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addDays(15)->timestamp,
            ],
            \env('JWT_REFRESH_SECRET')
        );

        return Token::refreshTokenFromString($token);
    }

    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes):Token
    {
        $token = SimpleJwt::customPayload(
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
        $payload = SimpleJwt::getPayload($token->value());
        $userId = $payload[self::USER_ID_KEY];

        if (!isset($userId)) {
            throw CannotGetClaim::byKey(self::USER_ID_KEY);
        }

        return UserId::fromString($userId);
    }

    /** @throws InvalidToken */
    public function validateSignature(Token $token):void
    {
        if ($token->isAnAccessToken() && !SimpleJwt::validate($token->value(), \env('JWT_ACCESS_SECRET'))) {
            throw InvalidToken::byAccessSignature();
        }

        if ($token->isARefreshToken() && !SimpleJwt::validate($token->value(), \env('JWT_REFRESH_SECRET'))) {
            throw InvalidToken::byRefreshSignature();
        }
    }

    /** @throws TokenExpired */
    public function validateExpiration(Token $token):void
    {
        if (!SimpleJwt::validateExpiration($token->value())) {
            throw TokenExpired::byExpirationDate();
        }
    }
}
