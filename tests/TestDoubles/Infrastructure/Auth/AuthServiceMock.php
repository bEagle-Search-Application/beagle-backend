<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Auth;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Shared\Application\Auth\AuthService;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;

final class AuthServiceMock implements AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws InvalidEmail
     * @throws UserNotFound
     * @throws InvalidPassword
     */
    public function generateTokenByUser(User $user):array
    {
        try
        {
            $this->userRepository->findByEmailAndPassword(
                $user->email(),
                $user->password(),
            );

            return [
                "token" => "jhdguferf87er6g87reg68er",
                "type" => "bearer",
            ];
        } catch (UserNotFound $e)
        {
            throw $e;
        }
    }
}
