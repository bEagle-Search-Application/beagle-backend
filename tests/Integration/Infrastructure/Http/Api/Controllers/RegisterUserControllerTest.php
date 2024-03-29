<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserVerificationTokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPhoneMotherObject;
use Tests\TestCase;

final class RegisterUserControllerTest extends TestCase
{
    private User $user;
    private UserRepository $userRepository;
    private UserVerificationTokenRepository $userVerificationRepository;
    private string $userPasswordWithoutHash;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->userVerificationRepository = $this->app->make(EloquentUserVerificationTokenRepository::class);

        $this->prepareUserForRegister();
    }

    private function prepareUserForRegister():void
    {
        $this->userPasswordWithoutHash = "12345678";
        $userPassword = UserPasswordMotherObject::create(
            $this->userPasswordWithoutHash
        );

        $this->user = UserMotherObject::createForRegister(
            userPassword: $userPassword
        );
    }

    /** @dataProvider invalidParametersProvider */
    public function testItReturnsBadRequestResponse(
        string $errorMessage,
        string $email,
        string $password,
        string $name,
        string $surname,
        string $phone_prefix,
        string $phone,
    ):void {
        $response = $this->post(
            \route('api.register'),
            [
                "email" => $email,
                "password" => $password,
                "name" => $name,
                "surname" => $surname,
                "phone_prefix" => $phone_prefix,
                "phone" => $phone,
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            $errorMessage,
            $decodedResponse["response"]
        );
    }

    public function invalidParametersProvider(): array
    {
        return [
            "Invalid email" => [
                "errorMessage" => "El email dani@noid tiene un formato inválido",
                "email" => "dani@noid",
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Invalid password" => [
                "errorMessage" => "El campo password debe contener al menos 8 caracteres.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => "1234",
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty name" => [
                "errorMessage" => "El campo name es obligatorio.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => "",
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty surname" => [
                "errorMessage" => "El campo surname es obligatorio.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => "",
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty phone_prefix" => [
                "errorMessage" => "El campo phone prefix es obligatorio.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => "",
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty phone" => [
                "errorMessage" => "El campo phone es obligatorio.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => "",
            ],
            "Invalid phone_prefix" => [
                "errorMessage" => "El prefijo telefónico 4444 es inválido",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => "4444",
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Invalid phone" => [
                "errorMessage" => "El teléfono dwadad tiene un formato inválido",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => "dwadad",
            ],
        ];
    }

    public function testItUserEmailAlreadyExists():void
    {
        $userRegistered = UserMotherObject::create();
        $this->userRepository->save($userRegistered);

        $response = $this->post(
            \route('api.register'),
            [
                "email" => $userRegistered->email()->value(),
                "password" => $this->userPasswordWithoutHash,
                "name" => $this->user->name(),
                "surname" => $this->user->surname(),
                "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                "phone" => $this->user->phone()->phoneAsString(),
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf("El email %s ya existe", $userRegistered->email()->value()),
            $decodedResponse["response"]
        );
    }

    public function testItRegistersUser():void
    {
        $userEmail = $this->user->email();

        $response = $this->post(
            \route('api.register'),
            [
                "email" => $userEmail->value(),
                "password" => $this->userPasswordWithoutHash,
                "name" => $this->user->name(),
                "surname" => $this->user->surname(),
                "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                "phone" => $this->user->phone()->phoneAsString(),
            ]
        );

        $expectedUser = $this->userRepository->findByEmail($userEmail);

        $expectedUserValidation = $this->userVerificationRepository->findByUserId($expectedUser->id());

        $this->assertSame(Response::HTTP_CREATED, $response->status());

        $this->assertTrue($userEmail->equals($expectedUser->email()));
        $this->assertTrue($expectedUser->password()->equals($this->user->password()));

        $this->assertTrue($expectedUserValidation->userId()->equals($expectedUser->id()));
    }
}
