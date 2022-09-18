<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\RegisterUser;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;

final class RegisterUser extends CommandHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @param RegisterUserCommand $command
     *
     * @throws InvalidValueObject
     * @throws CannotSaveUser
     */
    public function handle(Command $command):void
    {
        $this->userRepository->save(
            $this->createUser($command)
        );
    }

    /** @throws InvalidValueObject */
    public function createUser(RegisterUserCommand $command):User
    {
        $userId = UserId::fromString($command->userId());
        $userEmail = UserEmail::fromString($command->userEmail());
        $userPassword = UserPassword::fromString($command->userPassword());
        $userPhone = UserPhone::create(
            PhonePrefix::fromString($command->userPhonePrefix()),
            Phone::fromString($command->userPhone()),
        );

        return new User(
            $userId,
            $userEmail,
            $userPassword,
            $command->userName(),
            $command->userSurname(),
            null,
            null,
            $userPhone,
            null,
            true,
            0,
            null
        );
    }
}
