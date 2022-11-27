<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Query\User\Login\LoginQuery;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Guid;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;

final class LoginController extends BaseController
{
    public function execute():JsonResponse
    {
        try {
            $response = $this->queryBus->dispatch(
                new LoginQuery(
                    Guid::v4()->toBase58(),
                    Guid::v4()->toBase58(),
                    $this->request->get('email'),
                    \md5($this->request->get('password')),
                )
            );

            return $this->generateSuccessfulResponse($response->toArray());
        } catch (InvalidValueObject|UserNotFound $invalidParameters) {
            return $this->generateBadRequestResponse($invalidParameters->getMessage());
        }
    }
}
