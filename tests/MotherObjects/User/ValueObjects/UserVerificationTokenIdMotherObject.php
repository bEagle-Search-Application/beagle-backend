<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User\ValueObjects;

use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;

final class UserVerificationTokenIdMotherObject
{
    public static function create(?string $userVerificationTokenId = null):UserVerificationTokenId
    {
        return UserVerificationTokenId::fromString(
            $userVerificationTokenId ?? UserVerificationTokenId::v4()->toBase58()
        );
    }
}
