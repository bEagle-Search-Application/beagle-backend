<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\InvalidUserVerification;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidEmail;

final class AcceptUserVerificationEmail extends CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserVerificationRepository $userVerificationRepository
    ) {
    }

    /**
     * @param AcceptUserVerificationEmailCommand $command
     *
     * @throws InvalidUserVerification
     * @throws UserVerificationNotFound
     * @throws CannotSaveUser
     * @throws UserNotFound
     * @throws InvalidEmail
     */
    protected function handle(Command $command):void
    {
        $userEmail = UserEmail::fromString($command->userEmail());
        $userToken = UserToken::fromString($command->token());

        $userVerification = $this->userVerificationRepository->findByToken($userToken);
        $userVerification->ensureIfEmailToVerifyIsCorrect($userEmail);
        $userVerification->checkIfExpired();

        $user = $this->userRepository->findByEmail($userEmail);
        $user->verify();

        $this->userRepository->save($user);
    }
}
