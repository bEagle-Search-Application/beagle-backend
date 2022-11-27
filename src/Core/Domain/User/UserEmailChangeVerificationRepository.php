<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

interface UserEmailChangeVerificationRepository
{
    public function save(UserEmailChangeVerification $userChangeEmailVerification):void;
}
