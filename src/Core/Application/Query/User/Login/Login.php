<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Application\Auth\AuthService;
use Beagle\Shared\Bus\Query;
use Beagle\Shared\Bus\QueryHandler;
use Beagle\Shared\Bus\QueryResponse;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;

final class Login extends QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthService $authService
    )
    {
    }

    /**
     * @param LoginQuery $query
     * @return LoginResponse
     *
     * @throws InvalidEmail
     * @throws InvalidPassword
     * @throws UserNotFound
     * @throws CannotSaveUser
     */
    public function handle(Query $query):QueryResponse
    {
        $userEmail = UserEmail::fromString($query->email());
        $userPassword = UserPassword::fromString($query->password());

        $user = $this->userRepository->findByEmailAndPassword($userEmail, $userPassword);
        $token = $this->authService->generateTokenByUser($user);

        $user->updateToken(
            UserToken::fromString($token->value())
        );
        $this->userRepository->save($user);

        return new LoginResponse($user);
    }
}