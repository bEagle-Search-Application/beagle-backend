<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidTokenSignature;
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

            $response = $this->generateNoContentResponse();
        } catch (UserNotFound |InvalidEmail|CannotGetClaim $invalidArgumentsException) {
            $response = $this->generateBadRequestResponse($invalidArgumentsException->getMessage());
        } catch (UserVerificationNotFound $notFoundException) {
            $response = $this->generateNotFoundResponse($notFoundException->getMessage());
        } catch (TokenExpired|InvalidTokenSignature $unauthorizedException) {
            $response = $this->generateUnauthorizedResponse($unauthorizedException->getMessage());
        }

        return $response;
    }
}
