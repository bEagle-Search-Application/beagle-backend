<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Infrastructure\Email\Verification\UserEmailChangeVerificationSender;
use Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender;
use Beagle\Shared\Domain\ValueObjects\Token;

final class SpyUserEmailChangeVerificationSender implements UserEmailChangeVerificationSender
{
    private bool $isSent = false;

    public function execute(Token $token, UserEmail $userEmail):void
    {
        $this->isSent = true;
    }

    public function isSent():bool
    {
        return $this->isSent;
    }
}
