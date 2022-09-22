<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\InvalidUserVerification;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class AcceptUserVerificationEmailController extends BaseController
{
    public function execute(string $token, Request $request):JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new AcceptUserVerificationEmailCommand(
                    $request->get('email'),
                    $token
                )
            );

            return $this->generateNoContentResponse();
        } catch (UserNotFound |InvalidEmail|InvalidUserVerification $invalidArgumentsException) {
            return $this->generateBadRequestResponse($invalidArgumentsException->getMessage());
        } catch (UserVerificationNotFound $notFoundException) {
            return $this->generateNotFoundResponse($notFoundException->getMessage());
        }
    }
}
