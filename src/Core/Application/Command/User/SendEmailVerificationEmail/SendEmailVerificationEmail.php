<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\ValueObjects\Email;

final class SendEmailVerificationEmail extends CommandHandler
{
    public function __construct(
        private UserVerificationRepository $userVerificationRepository,
        private UserVerificationEmailSender $verificationEmailSender
    ) {
    }

    /**
     * @param SendEmailVerificationEmailCommand $command
     *
     * @throws InvalidEmail
     */
    protected function handle(Command $command):void
    {
        $userEmail = Email::fromString($command->userEmail());

        $userVerification = UserVerification::create($userEmail);
        $this->userVerificationRepository->save($userVerification);

        $this->verificationEmailSender->execute($userVerification);
    }
}
