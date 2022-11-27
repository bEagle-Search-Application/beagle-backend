<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\AcceptUserEmailChangeVerificationEmail;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserEmailChangeCannotBeValidated;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;

final class AcceptUserEmailChangeVerificationEmail extends CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserEmailChangeVerificationRepository $userEmailChangeVerificationRepository,
    ) {
    }

    /**
     * @param AcceptUserEmailChangeVerificationEmailCommand $command
     *
     * @throws UserEmailChangeCannotBeValidated
     * @throws InvalidValueObject
     * @throws CannotSaveUser
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $authorId = UserId::fromString($command->authorId());
        $userId = UserId::fromString($command->userId());

        $this->ensureIfAuthorIsTheSameThatUserToValidate($authorId, $userId);

        $userEmailChangeVerification = $this->userEmailChangeVerificationRepository->find($userId);
        $userEmailChangeVerification->confirm();
        $this->userEmailChangeVerificationRepository->save($userEmailChangeVerification);

        $user = $this->userRepository->find($userId);
        $user->updateEmailAfterVerify($userEmailChangeVerification->newEmail());
        $this->userRepository->save($user);
    }

    /** @throws UserEmailChangeCannotBeValidated */
    private function ensureIfAuthorIsTheSameThatUserToValidate(UserId $authorId, UserId $userId)
    {
        if (!$authorId->equals($userId)) {
            throw UserEmailChangeCannotBeValidated::byUser($authorId);
        }
    }
}
