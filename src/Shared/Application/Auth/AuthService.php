<?php declare(strict_types = 1);

namespace Beagle\Shared\Application\Auth;

use Beagle\Core\Domain\User\User;

interface AuthService
{
    public function generateTokenByUser(User $user): array;
}
