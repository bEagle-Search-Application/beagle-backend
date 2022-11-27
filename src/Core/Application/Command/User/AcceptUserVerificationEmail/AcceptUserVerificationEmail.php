<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserEmailVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\Errors\TokenExpired;

final class AcceptUserVerificationEmail extends CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserEmailVerificationRepository $userEmailVerificationRepository,
    ) {
    }

    /**
     * @param AcceptUserVerificationEmailCommand $command
     *
     * @throws TokenExpired
     * @throws InvalidValueObject
     * @throws UserVerificationNotFound
     * @throws CannotSaveUser
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $userId = UserId::fromString($command->userId());
        $this->userEmailVerificationRepository->findByUserId($userId);

        $user = $this->userRepository->find($userId);
        $user->verify();
        $this->userRepository->save($user);
    }
}
