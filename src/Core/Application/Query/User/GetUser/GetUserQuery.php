<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\GetUser;

use Beagle\Shared\Bus\Query;

final class GetUserQuery implements Query
{
    public function __construct(private string $userId)
    {
    }

    public function userId():string
    {
        return $this->userId;
    }
}
