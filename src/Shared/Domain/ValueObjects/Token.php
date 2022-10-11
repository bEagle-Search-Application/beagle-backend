<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenType;
use ReallySimpleJWT\Token as ReallySimpleJWT;

final class Token extends ReallySimpleJWT
{
    private const USER_ID_KEY = 'uid';

    private function __construct(
        private string $value,
        private TokenType $type
    ) {
    }

    public static function createAccessToken(UserId $userId):self
    {
        $tokenValue = self::customPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addMinutes(10)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        return new self($tokenValue, TokenType::ACCESS);
    }

    public static function createRefreshToken(UserId $userId):self
    {
        $tokenValue = self::customPayload(
            [
                'iat' => DateTime::now(),
                self::USER_ID_KEY => $userId->value(),
                'exp' => DateTime::now()->addDays(15)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_REFRESH_SECRET')
        );

        return new self($tokenValue, TokenType::REFRESH);
    }

    public static function createAccessTokenWithCustomPayload(array $payload):self
    {
        $tokenValue = self::customPayload(
            $payload,
            \env('JWT_ACCESS_SECRET')
        );

        return new self($tokenValue, TokenType::ACCESS);
    }

    public static function createRefreshTokenWithCustomPayload(array $payload):self
    {
        $tokenValue = self::customPayload(
            $payload,
            \env('JWT_REFRESH_SECRET')
        );

        return new self($tokenValue, TokenType::REFRESH);
    }

    /**
     * @throws TokenExpired
     * @throws InvalidTokenSignature
     */
    public static function accessTokenFromString(string $token):self
    {
        self::ensureIfIsEncryptByAccessTokenPassword($token);
        self::ensureIfTokenHasExpired($token);

        return new self($token, TokenType::ACCESS);
    }

    /**
     * @throws TokenExpired
     * @throws InvalidTokenSignature
     */
    public static function refreshTokenFromString(string $token):self
    {
        self::ensureIfIsEncryptByRefreshTokenPassword($token);
        self::ensureIfTokenHasExpired($token);

        return new self($token, TokenType::REFRESH);
    }

    /** @throws TokenExpired */
    private static function ensureIfTokenHasExpired(string $token):void
    {
        if (!self::validateExpiration($token)) {
            throw TokenExpired::byExpirationDate();
        }
    }

    /** @throws InvalidTokenSignature */
    private static function ensureIfIsEncryptByRefreshTokenPassword(string $token):void
    {
        if (!self::validate($token, \env('JWT_REFRESH_SECRET'))) {
            throw InvalidTokenSignature::byType(TokenType::REFRESH);
        }
    }

    /** @throws InvalidTokenSignature */
    private static function ensureIfIsEncryptByAccessTokenPassword(string $token):void
    {
        if (!self::validate($token, \env('JWT_ACCESS_SECRET'))) {
            throw InvalidTokenSignature::byType(TokenType::ACCESS);
        }
    }

    public function isARefreshToken():bool
    {
        return $this->type === TokenType::REFRESH;
    }

    public function isAnAccessToken():bool
    {
        return $this->type === TokenType::ACCESS;
    }

    public function value():string
    {
        return $this->value;
    }

    public function equals(Token $token):bool
    {
        return $this->value === $token->value();
    }
}
