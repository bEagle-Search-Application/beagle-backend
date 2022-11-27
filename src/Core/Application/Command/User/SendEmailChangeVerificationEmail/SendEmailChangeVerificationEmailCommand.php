<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\SendEmailChangeVerificationEmail;

use Beagle\Shared\Bus\Command;

final class SendEmailChangeVerificationEmailCommand implements Command
{
    public function __construct(
        private string $userId,
        private string $oldEmail,
        private string $newEmail
    ) {
    }

    public function userId():string
    {
        return $this->userId;
    }

    public function oldEmail():string
    {
        return $this->oldEmail;
    }

    public function newEmail():string
    {
        return $this->newEmail;
    }
}
