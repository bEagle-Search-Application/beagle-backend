<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\RegisterUser;

use Beagle\Shared\Bus\Command;

final class RegisterUserCommand implements Command
{
    public function __construct(
        private string $userId,
        private string $userEmail,
        private string $userPassword,
        private string $userName,
        private string $userSurname,
        private string $userPhone,
    ) {
    }

    public function userId():string
    {
        return $this->userId;
    }

    public function userEmail():string
    {
        return $this->userEmail;
    }

    public function userPassword():string
    {
        return $this->userPassword;
    }

    public function userName():string
    {
        return $this->userName;
    }

    public function userSurname():string
    {
        return $this->userSurname;
    }

    public function userPhone():string
    {
        return $this->userPhone;
    }
}
