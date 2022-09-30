<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

interface TokenService
{
    /** @throws InvalidToken */
    public function generateAccessToken(UserId $userId): Token;

    /** @throws InvalidToken */
    public function generateRefreshToken(UserId $userId): Token;

    /** @throws InvalidToken */
    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes): Token;

    /** @throws CannotGetClaim */
    public function userIdFromToken(Token $token):UserId;

    /** @throws InvalidToken */
    public function validateSignature(Token $token):void;

    /** @throws TokenExpired */
    public function validateExpiration(Token $token):void;
}
