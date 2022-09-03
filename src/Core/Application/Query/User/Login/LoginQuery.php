<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Shared\Bus\Query;

final class LoginQuery implements Query
{
    public function __construct(
        private string $email,
        private string $password
    )
    {
    }

    public function email():string
    {
        return $this->email;
    }

    public function password():string
    {
        return $this->password;
    }
}