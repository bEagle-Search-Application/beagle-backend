<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\EditUser\EditUserCommand;
use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserCannotBeEdited;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class EditUserController extends BaseController
{
    public function execute(string $userId):JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new EditUserCommand(
                    $this->getUserIdFromToken(),
                    $userId,
                    $this->request->get('email'),
                    $this->request->get('name'),
                    $this->request->get('surname'),
                    $this->request->get('phone_prefix'),
                    $this->request->get('phone'),
                    $this->request->get('location'),
                    $this->request->get('bio'),
                    $this->request->get('show_reviews'),
                )
            );

            $response = $this->generateNoContentResponse();
        } catch (InvalidValueObject|InvalidArgumentException|CannotSaveUser $invalidParametersException) {
            $response = $this->generateBadRequestResponse($invalidParametersException->getMessage());
        } catch (UserCannotBeEdited $forbiddenException) {
            $response = $this->generateForbiddenResponse($forbiddenException->getMessage());
        } catch (UserNotFound $notFoundException) {
            $response = $this->generateNotFoundResponse($notFoundException->getMessage());
        }

        return $response;
    }
}
