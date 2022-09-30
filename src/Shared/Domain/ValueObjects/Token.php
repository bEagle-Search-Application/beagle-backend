<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Shared\Domain\TokenType;

final class Token extends \ReallySimpleJWT\Token
{
    private function __construct(
        private string $value,
        private TokenType $type
    ) {
    }

    public static function accessTokenFromString(string $token):self
    {
        return new self($token, TokenType::ACCESS);
    }

    public static function refreshTokenFromString(string $token):self
    {
        return new self($token, TokenType::REFRESH);
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
