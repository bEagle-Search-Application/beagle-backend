<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;

final class InMemoryPersonalRefreshTokenRepository implements PersonalRefreshTokenRepository
{
    /** @var PersonalRefreshToken[]  */
    private array $personalRefreshTokens = [];

    public function save(PersonalRefreshToken $personalRefreshToken):void
    {
        $this->personalRefreshTokens[] = $personalRefreshToken;
    }

    public function findByUserId(UserId $userId):PersonalRefreshToken
    {
        foreach ($this->personalRefreshTokens as $personalAccessToken) {
            if ($personalAccessToken->userId()->equals($userId)) {
                return $personalAccessToken;
            }
        }

        throw PersonalRefreshTokenNotFound::byUserId($userId);
    }

    /** @throws CannotDeletePersonalRefreshToken */
    public function deleteByUserId(UserId $userId):void
    {
        foreach ($this->personalRefreshTokens as $key => $personalRefreshToken) {
            if ($personalRefreshToken->userId()->equals($userId)) {
                unset($this->personalRefreshTokens[$key]);
                return;
            }
        }

        throw CannotDeletePersonalRefreshToken::byUserId($userId);
    }
}
