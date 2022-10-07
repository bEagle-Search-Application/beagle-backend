<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\Logout;

use Beagle\Shared\Bus\Command;

final class LogoutCommand implements Command
{
    public function __construct(private string $userId)
    {
    }

    public function userId():string
    {
        return $this->userId;
    }
}
