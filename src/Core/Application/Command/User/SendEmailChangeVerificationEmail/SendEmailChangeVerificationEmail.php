<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\SendEmailChangeVerificationEmail;

use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Email\Verification\UserEmailChangeVerificationSender;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\TokenService;

final class SendEmailChangeVerificationEmail extends CommandHandler
{
    private const VERIFICATION_EXPIRATION_TIME_IN_MINUTES = 30;

    public function __construct(
        private UserRepository $userRepository,
        private UserEmailChangeVerificationRepository $userChangeEmailVerificationRepository,
        private TokenService $tokenService,
        private UserEmailChangeVerificationSender $userEmailChangeVerificationSender
    ) {
    }

    /**
     * @param SendEmailChangeVerificationEmailCommand $command
     *
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $userId = UserId::fromString($command->userId());
        $oldUserEmail = UserEmail::fromString($command->oldEmail());
        $newUserEmail = UserEmail::fromString($command->newEmail());

        $user = $this->userRepository->find($userId);

        $token = $this->tokenService->generateAccessTokenWithExpirationTime(
            $user->id(),
            self::VERIFICATION_EXPIRATION_TIME_IN_MINUTES
        );

        $userVerification = UserEmailChangeVerification::create(
            $userId,
            $oldUserEmail,
            $newUserEmail,
            $token
        );

        $this->userChangeEmailVerificationRepository->save($userVerification);

        $this->userEmailChangeVerificationSender->execute(
            $userVerification->token(),
            $newUserEmail
        );
    }
}
