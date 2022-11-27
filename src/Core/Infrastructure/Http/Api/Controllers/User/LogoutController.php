<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\Logout\LogoutCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;

final class LogoutController extends BaseController
{
    public function execute():JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new LogoutCommand(
                    $this->getUserIdFromToken()
                )
            );

            return $this->generateNoContentResponse();
        } catch (UserNotFound $e) {
            return $this->generateForbiddenResponse($e->getMessage());
        }
    }
}
