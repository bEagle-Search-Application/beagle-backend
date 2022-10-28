<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Token;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

final class JwtTokenService implements TokenService
{
    private const USER_ID_KEY = 'uid';

    public function generateAccessToken(UserId $userId):Token
    {
        return Token::createAccessToken($userId);
    }

    public function generateRefreshToken(UserId $userId):Token
    {
        return Token::createRefreshToken($userId);
    }

    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes):Token
    {
        return Token::createAccessTokenWithCustomPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addMinutes($minutes)->timestamp,
                'iss' => \env('APP_URL')
            ],
        );
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
}
