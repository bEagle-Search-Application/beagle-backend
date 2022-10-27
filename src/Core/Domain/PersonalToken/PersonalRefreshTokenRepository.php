<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

interface PersonalRefreshTokenRepository
{
    public function save(PersonalRefreshToken $personalRefreshToken):void;

    /**
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalRefreshToken
     * @throws PersonalRefreshTokenNotFound
     */
    public function findByUserIdAndToken(UserId $userId, Token $token):PersonalRefreshToken;

    /** @throws CannotDeletePersonalRefreshToken */
    public function deleteByUserId(UserId $userId):void;
}
