<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\RefreshToken;

use Beagle\Shared\Bus\Command;

final class RefreshTokenCommand implements Command
{
    public function __construct(
        private string $userId,
        private string $personalAccessTokenId
    ) {
    }

    public function userId():string
    {
        return $this->userId;
    }

    public function personalAccessTokenId():string
    {
        return $this->personalAccessTokenId;
    }
}
