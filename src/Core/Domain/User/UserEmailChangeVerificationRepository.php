<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserId;

interface UserEmailChangeVerificationRepository
{
    public function save(UserEmailChangeVerification $userChangeEmailVerification):void;

    public function find(UserId $userId):UserEmailChangeVerification;
}
