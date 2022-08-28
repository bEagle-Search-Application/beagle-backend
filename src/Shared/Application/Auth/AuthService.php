<?php declare(strict_types = 1);

namespace Beagle\Shared\Application\Auth;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserToken;

interface AuthService
{
    public function generateTokenByUser(User $user): UserToken;
}
