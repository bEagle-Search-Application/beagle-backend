<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\UserVerification;

interface UserVerificationEmailSender
{
    public function execute(UserVerification $userVerification):void;
}
