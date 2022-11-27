<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserEmailVerification
{
    public function __construct(
        private UserEmailVerificationId $id,
        private UserId $userId,
        private Token $token,
    ) {
    }

    public static function create(
        UserEmailVerificationId $id,
        UserId $userId,
        Token $token
    ):self {
        return new self(
            $id,
            $userId,
            $token,
        );
    }

    public function id():UserEmailVerificationId
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
