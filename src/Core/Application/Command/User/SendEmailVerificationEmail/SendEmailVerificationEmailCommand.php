<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Shared\Bus\Command;

final class SendEmailVerificationEmailCommand implements Command
{
    public function __construct(private string $userEmail)
    {
    }

    public function userEmail():string
    {
        return $this->userEmail;
    }
}
