<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\RefreshToken;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Command;
use Beagle\Shared\Bus\CommandHandler;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\TokenService;

final class RefreshToken extends CommandHandler
{
    public function __construct(
        private TokenService $tokenService,
        private PersonalAccessTokenRepository $personalAccessTokenRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param RefreshTokenCommand $command
     *
     * @throws InvalidPersonalAccessToken
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    protected function handle(Command $command):void
    {
        $userId = UserId::fromString($command->userId());
        $this->userRepository->find($userId);

        $accessToken = $this->tokenService->generateAccessToken($userId);
        $personalAccessToken = new PersonalAccessToken(
            PersonalTokenId::fromString($command->personalAccessTokenId()),
            $userId,
            $accessToken
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);
    }
}
