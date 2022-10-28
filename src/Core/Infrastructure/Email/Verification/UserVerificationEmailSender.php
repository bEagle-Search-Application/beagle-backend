<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email\Verification;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Shared\Domain\ValueObjects\Token;

interface UserVerificationEmailSender
{
    public function execute(Token $token, UserEmail $userEmail):void;
}
