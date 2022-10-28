<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Query\User\GetUser\GetUserQuery;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;

final class GetUserController extends BaseController
{
    public function execute(string $userId):JsonResponse
    {
        try {
            $response = $this->queryBus->dispatch(
                new GetUserQuery($userId)
            );

            return $this->generateSuccessfulResponse($response->toArray());
        } catch (InvalidValueObject $invalidParametersException) {
            return $this->generateBadRequestResponse($invalidParametersException->getMessage());
        } catch (UserNotFound $notFoundException) {
            return $this->generateNotFoundResponse($notFoundException->getMessage());
        }
    }
}
