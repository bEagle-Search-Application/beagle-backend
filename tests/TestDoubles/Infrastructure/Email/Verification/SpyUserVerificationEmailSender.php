<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender;
use Beagle\Shared\Domain\ValueObjects\Token;

final class SpyUserVerificationEmailSender implements UserVerificationEmailSender
{
    private bool $isSent = false;

    public function execute(Token $token, User $user):void
    {
        $this->isSent = true;
    }

    public function isSent():bool
    {
        return $this->isSent;
    }
}
