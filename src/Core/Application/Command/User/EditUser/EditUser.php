<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\EditUser;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserCannotBeEdited;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Bus\EventBus;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;
use InvalidArgumentException;

final class EditUser extends CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus
    ) {
    }

    /**
     * @param EditUserCommand $command
     *
     * @throws CannotSaveUser
     * @throws UserCannotBeEdited
     * @throws InvalidValueObject
     * @throws InvalidArgumentException
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $authorId = UserId::fromString($command->authorId());
        $userId = UserId::fromString($command->userId());
        $userEmail = UserEmail::fromString($command->email());
        $userName = $command->name();
        $userSurname = $command->surname();
        $userPhone = UserPhone::create(
            PhonePrefix::fromString($command->phonePrefix()),
            Phone::fromString($command->phone())
        );
        $userLocation = $command->location();
        $userBio = $command->bio();
        $userShowReviews = $command->showReviews();

        $this->ensureIfUserCanEditUserInformation($authorId, $userId);
        $user = $this->userRepository->find($userId);

        if (!$user->email()->equals($userEmail)) {
            $user->askForEmailChangeValidation($userEmail);
        }

        if (!$user->name() !== $userName) {
            $user->updateName($userName);
        }

        if (!$user->surname() !== $userSurname) {
            $user->updateSurname($userSurname);
        }

        if (!$user->phone()->equals($userPhone)) {
            $user->updatePhone($userPhone);
        }

        if (!$user->location() !== $userLocation) {
            $user->updateLocation($userLocation);
        }

        if (!$user->bio() !== $userBio) {
            $user->updateBio($userBio);
        }

        $userShowReviews ? $user->activeReviews() : $user->disableReviews();

        $this->userRepository->save($user);

        $this->eventBus->dispatch(...$user->pullEvents());
    }

    /** @throws UserCannotBeEdited */
    private function ensureIfUserCanEditUserInformation(UserId $authorId, UserId $userId)
    {
        if (!$authorId->equals($userId)) {
            throw UserCannotBeEdited::byUser($authorId);
        }
    }
}
