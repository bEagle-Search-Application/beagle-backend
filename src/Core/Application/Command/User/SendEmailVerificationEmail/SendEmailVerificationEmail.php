<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\SendEmailVerificationEmail;

use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserVerificationTokenId;
use Beagle\Core\Infrastructure\Email\Verification\UserVerificationEmailSender;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\TokenService;

final class SendEmailVerificationEmail extends CommandHandler
{
    private const VERIFICATION_EXPIRATION_TIME_IN_MINUTES = 30;

    public function __construct(
        private UserRepository $userRepository,
        private TokenService $tokenService,
        private UserVerificationTokenRepository $userVerificationRepository,
        private UserVerificationEmailSender $verificationEmailSender
    ) {
    }

    /**
     * @param SendEmailVerificationEmailCommand $command
     *
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $userEmail = UserEmail::fromString($command->userEmail());
        $userVerificationId = UserVerificationTokenId::fromString($command->userVerificationId());

        $user = $this->userRepository->findByEmail($userEmail);
        $accessToken = $this->tokenService->generateAccessTokenWithExpirationTime(
            $user->id(),
            self::VERIFICATION_EXPIRATION_TIME_IN_MINUTES
        );
        $userVerification = UserVerificationToken::create(
            $userVerificationId,
            $user->id(),
            $accessToken
        );
        $this->userVerificationRepository->save($userVerification);

        $this->verificationEmailSender->execute(
            $userVerification->token(),
            $user->email()
        );
    }
}
