<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmail;
use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserVerificationTokenRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\IdMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserVerificationTokenIdMotherObject;
use Tests\TestDoubles\Infrastructure\Email\Verification\SpyUserVerificationEmailSender;

final class SendEmailVerificationEmailTest extends TestCase
{
    private SendEmailVerificationEmail $sut;
    private UserVerificationTokenRepository $userVerificationRepository;
    private SpyUserVerificationEmailSender $spyUserVerificationEmailSender;
    private InMemoryUserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $tokenService = new JwtTokenService();
        $this->userVerificationRepository = new InMemoryUserVerificationTokenRepository();
        $this->spyUserVerificationEmailSender = new SpyUserVerificationEmailSender();

        $this->sut = new SendEmailVerificationEmail(
            $this->userRepository,
            $tokenService,
            $this->userVerificationRepository,
            $this->spyUserVerificationEmailSender
        );
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new SendEmailVerificationEmailCommand(
                IdMotherObject::create()->value(),
                "dani@n"
            )
        );
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new SendEmailVerificationEmailCommand(
                IdMotherObject::create()->value(),
                UserEmailMotherObject::create()->value()
            )
        );
    }

    public function testItCreatesAnUserVerification():void
    {
        $user = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($user);

        $userVerificationId = UserVerificationTokenIdMotherObject::create();

        $this->sut->__invoke(
            new SendEmailVerificationEmailCommand(
                $userVerificationId->value(),
                $user->email()->value()
            )
        );

        $userVerification = $this->userVerificationRepository->find($userVerificationId);

        $this->assertTrue($userVerification->id()->equals($userVerificationId));
        $this->assertTrue($user->id()->equals($userVerification->userId()));
        $this->assertTrue($this->spyUserVerificationEmailSender->isSent());
    }
}
