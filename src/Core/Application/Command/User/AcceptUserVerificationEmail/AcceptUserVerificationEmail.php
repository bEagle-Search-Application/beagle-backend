<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;

final class AcceptUserVerificationEmail extends CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserVerificationTokenRepository $verificationTokenRepository,
        private TokenService $tokenService
    ) {
    }

    /**
     * @param AcceptUserVerificationEmailCommand $command
     *
     * @throws TokenExpired
     * @throws CannotGetClaim
     * @throws InvalidValueObject
     * @throws UserVerificationNotFound
     * @throws CannotSaveUser
     * @throws UserNotFound
     */
    protected function handle(Command $command):void
    {
        $accessToken = Token::accessTokenFromString($command->token());
        $this->tokenService->validateSignature($accessToken);
        $this->tokenService->validateExpiration($accessToken);

        $userId = $this->tokenService->userIdFromToken($accessToken);
        $this->verificationTokenRepository->findByUserId($userId);

        $user = $this->userRepository->find($userId);
        $user->verify();
        $this->userRepository->save($user);
    }
}
