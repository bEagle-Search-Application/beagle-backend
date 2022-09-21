<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmail;
use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\InvalidUserVerification;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserVerificationRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserVerificationMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;

final class AcceptUserVerificationEmailTest extends TestCase
{
    private AcceptUserVerificationEmail $sut;
    private User $user;
    private UserRepository $userRepository;
    private UserVerification $userVerification;
    private InMemoryUserVerificationRepository $userVerificationRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->userVerificationRepository = new InMemoryUserVerificationRepository();

        $this->user = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($this->user);

        $this->userVerification = UserVerificationMotherObject::create(
            email: $this->user->email()
        );
        $this->userVerificationRepository->save($this->userVerification);

        $this->sut = new AcceptUserVerificationEmail(
            $this->userRepository,
            $this->userVerificationRepository
        );
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                'dani@kj',
                $this->userVerification->token()->value()
            )
        );
    }

    public function testItThrowsUserVerificationNotFoundException():void
    {
        $this->expectException(UserVerificationNotFound::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                UserEmailMotherObject::create()->value(),
                TokenMotherObject::create()->value()
            )
        );
    }

    public function testItThrowsInvalidUserVerificationExceptionIfUserEmailNotEqualToUserVerificationEmail():void
    {
        $this->expectException(InvalidUserVerification::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                UserEmailMotherObject::create()->value(),
                $this->userVerification->token()->value()
            )
        );
    }

    public function testItThrowsInvalidUserVerificationExceptionIfUserVerificationExpired():void
    {
        $expiredUserVerification = UserVerificationMotherObject::createExpired(
            email: $this->user->email()
        );
        $this->userVerificationRepository->save($expiredUserVerification);

        $this->expectException(InvalidUserVerification::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $expiredUserVerification->email()->value(),
                $expiredUserVerification->token()->value()
            )
        );
    }

    public function testItUserVerifiesEmail():void
    {
        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $this->userVerification->email()->value(),
                $this->userVerification->token()->value()
            )
        );

        $expectedUserVerification = $this->userVerificationRepository->findByEmail(
            $this->userVerification->email()
        );
        $expectedUser = $this->userRepository->findByEmail(
            $this->user->email()
        );

        $this->assertTrue($expectedUserVerification->token()->equals($this->userVerification->token()));
        $this->assertTrue($expectedUserVerification->email()->equals($this->userVerification->email()));
        $this->assertTrue($expectedUser->isVerified());
    }
}
