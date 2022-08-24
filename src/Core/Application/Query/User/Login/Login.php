<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Application\Auth\AuthService;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;

final class Login
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthService $authService
    )
    {
    }

    /**
     * @throws InvalidEmail
     * @throws InvalidPassword
     * @throws UserNotFound
     */
    public function handler(LoginQuery $query):LoginResponse
    {
        $userEmail = UserEmail::fromString($query->email());
        $userPassword = UserPassword::fromString($query->password());

        $user = $this->userRepository->findByEmailAndPassword($userEmail, $userPassword);
        $auth = $this->authService->generateTokenByUser($user);

        return new LoginResponse($user, $auth);
    }
}
