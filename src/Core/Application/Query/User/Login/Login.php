<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\PersonalToken\ValueObjects\PersonalTokenId;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Bus\Query;
use Beagle\Shared\Bus\QueryHandler;
use Beagle\Shared\Bus\QueryResponse;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\TokenService;

final class Login extends QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenService $personalTokenService,
        private PersonalAccessTokenRepository $personalAccessTokenRepository,
        private PersonalRefreshTokenRepository $personalRefreshTokenRepository
    ) {
    }

    /**
     * @param LoginQuery $query
     *
     * @return LoginResponse
     *
     * @throws InvalidPersonalRefreshToken
     * @throws InvalidPersonalAccessToken
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    public function handle(Query $query):QueryResponse
    {
        $userEmail = UserEmail::fromString($query->email());
        $userPassword = UserPassword::fromString($query->password());
        $personalAccessTokenId = PersonalTokenId::fromString($query->accessTokenId());
        $personalRefreshTokenId = PersonalTokenId::fromString($query->accessTokenId());

        $user = $this->userRepository->findByEmailAndPassword($userEmail, $userPassword);

        $personalAccessToken = $this->createPersonalAccessToken($user->id(), $personalAccessTokenId);
        $this->personalAccessTokenRepository->save($personalAccessToken);

        $personalRefreshToken = $this->createPersonalRefreshToken($user->id(), $personalRefreshTokenId);
        $this->personalRefreshTokenRepository->save($personalRefreshToken);

        return new LoginResponse(
            $user,
            $personalAccessToken,
            $personalRefreshToken
        );
    }

    /** @throws InvalidPersonalAccessToken
     * @throws InvalidToken
     */
    public function createPersonalAccessToken(UserId $userId, PersonalTokenId $tokenId):PersonalAccessToken
    {
        $accessToken = $this->personalTokenService->generateAccessToken($userId);
        return new PersonalAccessToken(
            $tokenId,
            $userId,
            $accessToken
        );
    }

    /** @throws InvalidPersonalRefreshToken
     * @throws InvalidToken
     */
    public function createPersonalRefreshToken(UserId $userId, PersonalTokenId $tokenId):PersonalRefreshToken
    {
        $refreshToken = $this->personalTokenService->generateRefreshToken($userId);
        return new PersonalRefreshToken(
            $tokenId,
            $userId,
            $refreshToken
        );
    }
}
