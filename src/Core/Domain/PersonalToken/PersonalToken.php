<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\PersonalToken;

use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Domain\ValueObjects\Token;

class PersonalToken
{
    protected function __construct(
        private PersonalTokenId $id,
        private UserId $userId,
        private Token $token,
    ) {
    }

    public function id():PersonalTokenId
    {
        return $this->id;
    }

    public function userId():UserId
    {
        return $this->userId;
    }

    public function token():Token
    {
        return $this->token;
    }
}
