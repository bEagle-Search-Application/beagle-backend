<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidToken;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Beagle\Shared\Infrastructure\Token\Errors\CannotGetClaim;
use Illuminate\Http\JsonResponse;

final class AcceptUserVerificationEmailController extends BaseController
{
    public function execute(string $token):JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new AcceptUserVerificationEmailCommand($token)
            );

            return $this->generateNoContentResponse();
        } catch (UserNotFound |InvalidEmail|CannotGetClaim|TokenExpired $invalidArgumentsException) {
            return $this->generateBadRequestResponse($invalidArgumentsException->getMessage());
        } catch (UserVerificationNotFound|InvalidToken $notFoundException) {
            return $this->generateNotFoundResponse($notFoundException->getMessage());
        }
    }
}
