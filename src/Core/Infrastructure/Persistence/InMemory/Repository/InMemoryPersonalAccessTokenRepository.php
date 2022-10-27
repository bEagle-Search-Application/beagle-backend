<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\InMemory\Repository;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;

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

    /** @throws CannotDeletePersonalAccessToken */
    public function deleteByUserId(UserId $userId):void
    {
        foreach ($this->personalAccessTokens as $key => $personalAccessToken) {
            if ($personalAccessToken->userId()->equals($userId)) {
                unset($this->personalAccessTokens[$key]);
                return;
            }
        }

        throw CannotDeletePersonalAccessToken::byUserId($userId);
    }

    public function findByUserIdAndToken(UserId $userId, Token $token):PersonalAccessToken
    {
        foreach ($this->personalAccessTokens as $personalAccessToken) {
            if ($this->userIdAndTokenAreEquals($personalAccessToken, $userId, $token)) {
                return $personalAccessToken;
            }
        }

        throw PersonalAccessTokenNotFound::byUserIdAndToken($userId);
    }

    private function userIdAndTokenAreEquals(
        PersonalAccessToken $personalAccessToken,
        UserId $userId,
        Token $personalTokenId
    ):bool {
        return $personalAccessToken->userId()->equals($userId)
               && $personalAccessToken->token()->equals($personalTokenId);
    }
}
