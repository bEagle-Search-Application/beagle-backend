<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\SendEmailChangeVerificationEmail;

use Beagle\Core\Application\Command\User\SendEmailChangeVerificationEmail\SendEmailChangeVerificationEmail;
use Beagle\Core\Application\Command\User\SendEmailChangeVerificationEmail\SendEmailChangeVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserEmailChangeVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestDoubles\Infrastructure\Email\Verification\SpyUserEmailChangeVerificationSender;

final class SendEmailChangeVerificationEmailTest extends TestCase
{
    private SendEmailChangeVerificationEmail $sut;
    private UserEmailChangeVerificationRepository $userChangeEmailVerificationRepository;
    private SpyUserEmailChangeVerificationSender $spyUserEmailChangeVerificationSender;
    private InMemoryUserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $tokenService = new JwtTokenService();
        $this->userChangeEmailVerificationRepository = new InMemoryUserEmailChangeVerificationRepository();
        $this->spyUserEmailChangeVerificationSender = new SpyUserEmailChangeVerificationSender();

        $this->sut = new SendEmailChangeVerificationEmail(
            $this->userRepository,
            $this->userChangeEmailVerificationRepository,
            $tokenService,
            $this->spyUserEmailChangeVerificationSender
        );
    }

    /** @dataProvider invalidEmailsProvider */
    public function testItThrowsInvalidEmailExceptionIfEmailsAreInvalid(
        string $oldEmail,
        string $newEmail,
    ):void {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new SendEmailChangeVerificationEmailCommand(
                UserIdMotherObject::create()->value(),
                $oldEmail,
                $newEmail,
            )
        );
    }

    public function invalidEmailsProvider():array
    {
        return [
            "Invalid old email" => [
                "oldEmail" => "fsddf@@@@",
                "newEmail" => UserEmailMotherObject::create()->value()
            ],
            "Invalid new email" => [
                "oldEmail" => UserEmailMotherObject::create()->value(),
                "newEmail" => "sdfdsf@dd"
            ],
        ];
    }

    public function testItThrowsUserNotFoundExceptionIfUserDoesNotExist():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new SendEmailChangeVerificationEmailCommand(
                UserIdMotherObject::create()->value(),
                UserEmailMotherObject::create()->value(),
                UserEmailMotherObject::create()->value(),
            )
        );
    }

    public function testItCreatesAnUserEmailChangeVerification():void
    {
        $user = UserMotherObject::create();
        $this->userRepository->save($user);

        $newEmail = UserEmailMotherObject::create();

        $this->sut->__invoke(
            new SendEmailChangeVerificationEmailCommand(
                $user->id()->value(),
                $user->email()->value(),
                $newEmail->value()
            )
        );

        $userEmailChangeVerification = $this->userChangeEmailVerificationRepository->find($user->id());

        $this->assertTrue($userEmailChangeVerification->userId()->equals($user->id()));
        $this->assertTrue($userEmailChangeVerification->oldEmail()->equals($user->email()));
        $this->assertTrue($userEmailChangeVerification->newEmail()->equals($newEmail));
        $this->assertTrue($this->spyUserEmailChangeVerificationSender->isSent());
    }
}
