<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;

final class UserVerificationTokenIdMotherObject
{
    public static function create(?string $userId = null):UserVerificationTokenId
    {
        return empty($userId)
            ? UserVerificationTokenId::fromString(
                UserVerificationTokenId::v4()->toBase58()
            )
            : UserVerificationTokenId::fromString($userId);
    }
}
