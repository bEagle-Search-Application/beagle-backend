<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserCannotBeValidated;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Shared\Bus\CommandBus;
use Beagle\Shared\Bus\QueryBus;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Domain\TokenService;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AcceptUserVerificationEmailController extends BaseController
{
    public function __construct(
        CommandBus $commandBus,
        QueryBus $queryBus,
        Request $request,
        private TokenService $tokenService
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        parent::__construct($commandBus, $queryBus, $request);
    }

    public function execute(string $userId, string $token):JsonResponse
    {
        try {
            $accessToken = Token::accessTokenFromString($token);
            $userIdFromToken = $this->tokenService->userIdFromToken($accessToken);

            $this->commandBus->dispatch(
                new AcceptUserVerificationEmailCommand(
                    $userId,
                    $userIdFromToken->value()
                )
            );

            $response = $this->generateNoContentResponse();
        } catch (UserVerificationNotFound|UserNotFound|CannotGetClaim|UserCannotBeValidated $forbiddenException) {
            $response = $this->generateForbiddenResponse($forbiddenException->getMessage());
        } catch (TokenExpired|InvalidTokenSignature $unauthorizedException) {
            $response = $this->generateUnauthorizedResponse($unauthorizedException->getMessage());
        }

        return $response;
    }
}
