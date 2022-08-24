<?php declare(strict_types = 1);

namespace Beagle\Shared\Infrastructure\Auth;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserDataTransformer;
use Beagle\Shared\Application\Auth\AuthService;
use Beagle\Shared\Domain\Errors\UserNotFound;
use Illuminate\Support\Facades\Auth;

final class JwtAuthService implements AuthService
{
    public function __construct(private UserDataTransformer $userDataTransformer)
    {
    }

    /** @throws UserNotFound */
    public function generateTokenByUser(User $user):array
    {
        $userDao = $this->userDataTransformer->fromUser($user);
        $token = Auth::login($userDao);

        if (!$token)
        {
            throw UserNotFound::byCredentials($user->email());
        }

        return [
            "token" => $token,
            "type" => "bearer",
        ];
    }
}
