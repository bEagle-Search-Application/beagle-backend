<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;

interface PersonalRefreshTokenRepository
{
    public function save(PersonalRefreshToken $personalRefreshToken):void;

    /** @throws InvalidPersonalRefreshToken
     * @throws PersonalRefreshTokenNotFound
     */
    public function findByUserId(UserId $userId):PersonalRefreshToken;
}
