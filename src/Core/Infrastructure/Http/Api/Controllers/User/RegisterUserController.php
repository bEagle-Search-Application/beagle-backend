<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Http\Api\Controllers\User;

use Beagle\Core\Application\Command\User\RegisterUser\RegisterUserCommand;
use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Infrastructure\Http\Api\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Uid\Uuid;

final class RegisterUserController extends BaseController
{
    public function execute():JsonResponse
    {
        try {
            $this->request->validate([
                "email" => 'required|email',
                "password" => 'required|min:8',
                "name" => 'required',
                "surname" => 'required',
                "phone_prefix" => 'required',
                "phone" => 'required'
            ]);

            $this->commandBus->dispatch(
                new RegisterUserCommand(
                    Uuid::v4()->toBase58(),
                    $this->request->get('email'),
                    \md5($this->request->get('password')),
                    $this->request->get('name'),
                    $this->request->get('surname'),
                    $this->request->get('phone_prefix'),
                    $this->request->get('phone'),
                )
            );

            return $this->generateCreatedResponse();
        } catch (CannotSaveUser|InvalidValueObject|ValidationException $invalidParameters) {
            return $this->generateBadRequestResponse($invalidParameters->getMessage());
        }
    }
}
