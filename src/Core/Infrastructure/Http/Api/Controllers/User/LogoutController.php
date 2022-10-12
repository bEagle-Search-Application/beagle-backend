<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\Logout\LogoutCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token;

final class LogoutController extends BaseController
{
    public function execute(Request $request):JsonResponse
    {
        try {
            $token = $this->tokenFromHeaderAsString($request->header('authorization'));
            $userIdAsString = Token::getPayload($token)['uid'];
            $userId = UserId::fromString($userIdAsString);

            $this->commandBus->dispatch(
                new LogoutCommand($userId->value())
            );

            return $this->generateNoContentResponse();
        } catch (UserNotFound $e) {
            return $this->generateForbiddenResponse($e->getMessage());
        }
    }

    private function tokenFromHeaderAsString(string $header):string
    {
        return \explode(" ", $header)[1];
    }
}
