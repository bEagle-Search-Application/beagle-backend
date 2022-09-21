<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmail;
use Beagle\Core\Application\Command\User\SendEmailVerificationEmail\SendEmailVerificationEmailCommand;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserVerificationRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;

final class SendEmailVerificationEmailTest extends TestCase
{
    private SendEmailVerificationEmail $sut;
    private UserVerificationRepository $userVerificationRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userVerificationRepository = new InMemoryUserVerificationRepository();

        $this->sut = new SendEmailVerificationEmail(
            $this->userVerificationRepository,
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
    }
}
