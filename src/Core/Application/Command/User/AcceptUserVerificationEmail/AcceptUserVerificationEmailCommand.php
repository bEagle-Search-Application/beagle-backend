<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Shared\Bus\Command;

final class AcceptUserVerificationEmailCommand implements Command
{
    public function __construct(private string $userId)
    {
    }

    public function userId():string
    {
        return $this->userId;
    }
}
