<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Shared\Domain\ValueObjects\Email;

interface UserVerificationRepository
{
    public function save(UserVerification $userVerification):void;

    public function findByEmail(Email $email):UserVerification;
}
