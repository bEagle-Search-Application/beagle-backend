<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

interface TokenService
{
    /** @throws InvalidTokenSignature */
    public function generateAccessToken(UserId $userId): Token;

    /** @throws InvalidTokenSignature */
    public function generateRefreshToken(UserId $userId): Token;

    /** @throws InvalidTokenSignature */
    public function generateAccessTokenWithExpirationTime(UserId $userId, int $minutes): Token;

    /** @throws CannotGetClaim */
    public function userIdFromToken(Token $token):UserId;
}
