<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\Logout;

use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\CannotDeletePersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;

final class Logout extends CommandHandler
{
    public function __construct(
        private PersonalAccessTokenRepository $personalAccessTokenRepository,
        private PersonalRefreshTokenRepository $personalRefreshTokenRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param LogoutCommand $command
     *
     * @throws CannotDeletePersonalRefreshToken
     * @throws CannotDeletePersonalAccessToken
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $userId = UserId::fromString($command->userId());
        $this->userRepository->find($userId);

        $this->personalAccessTokenRepository->deleteByUserId($userId);
        $this->personalRefreshTokenRepository->deleteByUserId($userId);
    }
}
