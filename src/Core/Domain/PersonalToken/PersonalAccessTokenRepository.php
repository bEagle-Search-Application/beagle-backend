<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Token;

interface PersonalAccessTokenRepository
{
    public function save(PersonalAccessToken $personalAccessToken):void;

    /**
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws InvalidTokenSignature
     * @throws PersonalAccessTokenNotFound
     */
    public function findByUserIdAndToken(
        UserId $userId,
        Token $token
    ):PersonalAccessToken;

    /** @throws CannotDeletePersonalAccessToken */
    public function deleteByUserId(UserId $userId):void;
}
