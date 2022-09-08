<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Application\Query\User\Login\Login;
use Beagle\Core\Application\Query\User\Login\LoginQuery;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;
use Beagle\Shared\Domain\Errors\UserNotFound;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LoginController extends BaseController
{
    public function execute(Request $request):JsonResponse
    {
        try
        {
            $response = $this->queryBus->dispatch(
                new LoginQuery(
                    $request->get('email'),
                    \md5($request->get('password')),
                )
            );

            return $this->generateSuccessfulResponse($response->toArray());
        } catch (InvalidEmail|InvalidPassword|UserNotFound $invalidParameters)
        {
            return $this->generateBadRequestResponse($invalidParameters->getMessage());
        }
    }
}
