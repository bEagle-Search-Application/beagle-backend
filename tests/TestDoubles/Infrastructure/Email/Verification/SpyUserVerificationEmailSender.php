<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender;

final class SpyUserVerificationEmailSender implements UserVerificationEmailSender
{
    private bool $isSent = false;

    public function execute(UserVerification $userVerification):void
    {
        $this->isSent = true;
    }

    public function isSent():bool
    {
        return $this->isSent;
    }
}
