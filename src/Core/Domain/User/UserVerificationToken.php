<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserVerificationToken
{
    public function __construct(
        private UserVerificationTokenId $id,
        private UserId $userId,
        private Token $token,
    ) {
    }

    public static function create(
        UserVerificationTokenId $id,
        UserId $userId,
        Token $token
    ):self {
        return new self(
            $id,
            $userId,
            $token,
        );
    }

    public function id():UserVerificationTokenId
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
