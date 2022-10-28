<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\RefreshToken\RefreshTokenCommand;
use Beagle\Core\Domain\PersonalToken\Errors\InvalidPersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\CommandBus;
use Beagle\Shared\Bus\QueryBus;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\ValueObjects\Guid;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token;

final class RefreshTokenController extends BaseController
{
    public function __construct(
        protected CommandBus $commandBus,
        protected QueryBus $queryBus,
        private PersonalAccessTokenRepository $personalAccessTokenRepository
    ) {
        parent::__construct($this->commandBus, $this->queryBus);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidTokenSignature
     * @throws TokenExpired
     * @throws InvalidPersonalAccessToken
     * @throws PersonalAccessTokenNotFound
     */
    public function execute(Request $request):JsonResponse
    {
        try {
            $token = $this->tokenFromHeaderAsString($request->header('authorization'));
            $userIdAsString = Token::getPayload($token)['uid'];
            $userId = UserId::fromString($userIdAsString);

            $this->commandBus->dispatch(
                new RefreshTokenCommand(
                    $userId->value(),
                    Guid::v4()->toBase58()
                )
            );

            $personalAccessToken = $this->personalAccessTokenRepository->findByUserId($userId);

            return $this->generateSuccessfulResponse(
                [
                    "access_token" => $personalAccessToken->token()->value()
                ]
            );
        } catch (UserNotFound $notFound) {
            return $this->generateForbiddenResponse($notFound->getMessage());
        }
    }

    private function tokenFromHeaderAsString(string $header):string
    {
        return \explode(" ", $header)[1];
    }
}
