<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserEmailVerificationId;

final class UserEmailVerificationIdMotherObject
{
    public static function create(?string $userVerificationTokenId = null):UserEmailVerificationId
    {
        return UserEmailVerificationId::fromString(
            $userVerificationTokenId ?? UserEmailVerificationId::v4()->toBase58()
        );
    }
}
