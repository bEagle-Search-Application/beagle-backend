<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Shared\Domain\ValueObjects\Token;

final class TokenMotherObject
{
    public static function create(?string $token = null):Token
    {
        return empty($token)
            ? Token::fromString(Token::generateRandom64String()->value())
            : Token::fromString($token);
    }
}
