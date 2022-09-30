<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;

final class InMemoryPersonalAccessTokenRepository implements PersonalAccessTokenRepository
{
    /** @var PersonalAccessToken[]  */
    private array $personalAccessTokens = [];

    public function save(PersonalAccessToken $personalAccessToken):void
    {
        foreach ($this->personalAccessTokens as $key => $inMemoryPersonalAccessToken) {
            if ($inMemoryPersonalAccessToken->userId()->equals($personalAccessToken->userId())) {
                $this->personalAccessTokens[$key] = $personalAccessToken;
                return;
            }
        }

        $this->personalAccessTokens[] = $personalAccessToken;
    }

    public function findByUserId(UserId $userId):PersonalAccessToken
    {
        foreach ($this->personalAccessTokens as $personalAccessToken) {
            if ($personalAccessToken->userId()->equals($userId)) {
                return $personalAccessToken;
            }
        }

        throw PersonalAccessTokenNotFound::byUserId($userId);
    }
}
