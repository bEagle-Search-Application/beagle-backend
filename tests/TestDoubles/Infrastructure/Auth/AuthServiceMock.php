<?php declare(strict_types = 1);

namespace Tests\TestDoubles\Infrastructure\Auth;

use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Application\Auth\AuthService;
use Beagle\Shared\Domain\Errors\InvalidValueObject;

final class AuthServiceMock implements AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function generateTokenByUser(User $user):UserToken
    {
        try {
            $this->userRepository->findByEmailAndPassword(
                $user->email(),
                $user->password(),
            );

            return UserToken::fromString("jhdguferf87er6g87reg68er");
        } catch (UserNotFound $e) {
            throw $e;
        }
    }
}
