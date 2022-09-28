<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\UserVerification;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Email;
use Beagle\Shared\Domain\ValueObjects\Guid;
use Beagle\Shared\Domain\ValueObjects\Token;
use Tests\MotherObjects\DateTimeMotherObject;
use Tests\MotherObjects\EmailMotherObject;
use Tests\MotherObjects\IdMotherObject;
use Tests\MotherObjects\TokenMotherObject;

final class UserVerificationMotherObject
{
    /** @throws InvalidValueObject */
    public static function create(
        ?Guid $id = null,
        ?Email $email = null,
        ?Token $token = null,
        ?DateTime $expiredAt = null,
    ):UserVerification {
        return new UserVerification(
            empty($id) ? IdMotherObject::create() : $id,
            empty($email) ? EmailMotherObject::create() : $email,
            empty($token) ? TokenMotherObject::create() : $token,
            empty($expiredAt) ? DateTimeMotherObject::inFuture(30) : $expiredAt,
        );
    }

    /** @throws InvalidValueObject */
    public static function createExpired(
        ?Guid $id = null,
        ?Email $email = null,
        ?Token $token = null,
    ):UserVerification {
        return new UserVerification(
            empty($id) ? IdMotherObject::create() : $id,
            empty($email) ? EmailMotherObject::create() : $email,
            empty($token) ? TokenMotherObject::create() : $token,
            DateTimeMotherObject::yesterday()
        );
    }
}
