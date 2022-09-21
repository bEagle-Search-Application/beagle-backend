<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmail;
use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmailCommand;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserVerificationRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\TestDoubles\Infrastructure\Email\Verification\SpyUserVerificationEmailSender;

final class SendEmailVerificationEmailTest extends TestCase
{
    private SendEmailVerificationEmail $sut;
    private UserVerificationRepository $userVerificationRepository;
    private SpyUserVerificationEmailSender $spyUserVerificationRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userVerificationRepository = new InMemoryUserVerificationRepository();
        $this->spyUserVerificationRepository = new SpyUserVerificationEmailSender();

        $this->sut = new SendEmailVerificationEmail(
            $this->userVerificationRepository,
            $this->spyUserVerificationRepository
        );
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new SendEmailVerificationEmailCommand("dani@n")
        );
    }

    public function testItCreatesAnUserVerification():void
    {
        $userEmail = UserEmailMotherObject::create();

        $this->sut->__invoke(
            new SendEmailVerificationEmailCommand(
                $userEmail->value()
            )
        );

        $userVerification = $this->userVerificationRepository->findByEmail($userEmail);

        $this->assertTrue($userVerification->email()->equals($userEmail));
        $this->assertTrue($this->spyUserVerificationRepository->isSent());
    }
}
