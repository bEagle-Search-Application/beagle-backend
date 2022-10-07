<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;

interface PersonalAccessTokenRepository
{
    public function save(PersonalAccessToken $personalAccessToken):void;

    /** @throws InvalidPersonalAccessToken
     * @throws PersonalAccessTokenNotFound
     */
    public function findByUserId(UserId $userId):PersonalAccessToken;

    /** @throws CannotDeletePersonalAccessToken */
    public function deleteByUserId(UserId $userId):void;
}
