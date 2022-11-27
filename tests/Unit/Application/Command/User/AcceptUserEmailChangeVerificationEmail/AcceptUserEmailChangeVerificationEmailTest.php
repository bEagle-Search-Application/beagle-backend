<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\AcceptUserEmailChangeVerificationEmail;

use Beagle\Core\Application\Command\User\AcceptUserEmailChangeVerificationEmail\AcceptUserEmailChangeVerificationEmail;
use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserEmailChangeCannotBeValidated;
use Beagle\Core\Domain\User\Errors\UserEmailChangeVerificationNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserEmailChangeVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserEmailChangeVerificationMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class AcceptUserEmailChangeVerificationEmailTest extends TestCase
{
    private AcceptUserEmailChangeVerificationEmail $sut;
    private User $user;
    private UserRepository $userRepository;
    private UserEmailChangeVerification $userChangeEmailVerification;
    private InMemoryUserEmailChangeVerificationRepository $userEmailChangeVerificationRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->userEmailChangeVerificationRepository = new InMemoryUserEmailChangeVerificationRepository();

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->userChangeEmailVerification = UserEmailChangeVerificationMotherObject::create(
            $this->user->id(),
            $this->user->email(),
            UserEmailMotherObject::create()
        );
        $this->userEmailChangeVerificationRepository->save($this->userChangeEmailVerification);

        $this->sut = new AcceptUserEmailChangeVerificationEmail(
            $this->userRepository,
            $this->userEmailChangeVerificationRepository,
        );
    }

    public function testItThrowsUserEmailChangeCannotBeValidatedExceptionIfAuthorAndUSerAreNotTheSame():void
    {
        $this->expectException(UserEmailChangeCannotBeValidated::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                UserIdMotherObject::create()->value(),
                UserIdMotherObject::create()->value(),
            )
        );
    }

    public function testItThrowsUserVerificationNotFoundException():void
    {
        $this->expectException(UserEmailChangeVerificationNotFound::class);

        $userId = UserIdMotherObject::create();

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $userId->value(),
                $userId->value(),
            )
        );
    }

    public function testItUserVerifiesEmailChange():void
    {
        $userWithOldEmail = $this->userRepository->findByEmail(
            $this->user->email()
        );
        $this->assertTrue($userWithOldEmail->email()->equals($this->user->email()));

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $this->user->id()->value(),
                $this->user->id()->value()
            )
        );

        $expectedUserVerification = $this->userEmailChangeVerificationRepository->find(
            $this->userChangeEmailVerification->userId()
        );
        $expectedUserWithNewEmail = $this->userRepository->findByEmail(
            $this->user->email()
        );

        $this->assertTrue($expectedUserVerification->confirmed());
        $this->assertTrue($expectedUserWithNewEmail->email()->equals($this->userChangeEmailVerification->newEmail()));
    }
}
