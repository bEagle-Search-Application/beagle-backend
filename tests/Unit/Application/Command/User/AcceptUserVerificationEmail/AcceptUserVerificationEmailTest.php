<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmail;
use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserCannotBeValidated;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserEmailVerificationRepository;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserEmailVerificationMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class AcceptUserVerificationEmailTest extends TestCase
{
    private AcceptUserVerificationEmail $sut;
    private User $user;
    private UserRepository $userRepository;
    private UserEmailVerification $userVerification;
    private InMemoryUserEmailVerificationRepository $userVerificationTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->userVerificationTokenRepository = new InMemoryUserEmailVerificationRepository();

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->userVerification = UserEmailVerificationMotherObject::create(
            userId: $this->user->id()
        );
        $this->userVerificationTokenRepository->save($this->userVerification);

        $this->sut = new AcceptUserVerificationEmail(
            $this->userRepository,
            $this->userVerificationTokenRepository,
        );
    }

    public function testItThrowsUserCannotBeValidatedExceptionIfAuthorAndUSerAreNotTheSame():void
    {
        $this->expectException(UserCannotBeValidated::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                UserIdMotherObject::create()->value(),
                UserIdMotherObject::create()->value(),
            )
        );
    }

    public function testItThrowsUserVerificationNotFoundException():void
    {
        $this->expectException(UserVerificationNotFound::class);

        $userId = UserIdMotherObject::create();

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $userId->value(),
                $userId->value(),
            )
        );
    }

    public function testItUserVerifiesEmail():void
    {
        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $this->user->id()->value(),
                $this->user->id()->value()
            )
        );

        $expectedUserVerification = $this->userVerificationTokenRepository->findByUserId(
            $this->userVerification->userId()
        );
        $expectedUser = $this->userRepository->findByEmail(
            $this->user->email()
        );

        $this->assertTrue($expectedUserVerification->token()->equals($this->userVerification->token()));
        $this->assertTrue($expectedUserVerification->userId()->equals($this->userVerification->userId()));
        $this->assertTrue($expectedUser->isVerified());
    }
}
