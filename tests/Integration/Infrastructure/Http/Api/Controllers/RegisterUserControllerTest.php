<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
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
    private UserPassword $userPassword;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);

        $this->prepareUserForRegister();
    }

    private function prepareUserForRegister():void
    {
        $this->userPassword = UserPasswordMotherObject::create();

        $this->user = UserMotherObject::createForRegister(
            userPassword: $this->userPassword
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
            \route(
                'api.register',
                [
                    "email" => $email,
                    "password" => $password,
                    "name" => $name,
                    "surname" => $surname,
                    "phone_prefix" => $phone_prefix,
                    "phone" => $phone,
                ]
            )
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
                "errorMessage" => "The email dani@noid has an invalid format",
                "email" => "dani@noid",
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Invalid password" => [
                "errorMessage" => "The password must be at least 8 characters.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => "1234",
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty name" => [
                "errorMessage" => "The name field is required.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => "",
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty surname" => [
                "errorMessage" => "The surname field is required.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => "",
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty phone_prefix" => [
                "errorMessage" => "The phone prefix field is required.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => "",
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Empty phone" => [
                "errorMessage" => "The phone field is required.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => UserPhoneMotherObject::create()->phonePrefixAsString(),
                "phone" => "",
            ],
            "Invalid phone_prefix" => [
                "errorMessage" => "The phone code 4444 is invalid",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => UserPasswordMotherObject::create()->value(),
                "name" => StringMotherObject::createName(),
                "surname" => StringMotherObject::createSurname(),
                "phone_prefix" => "4444",
                "phone" => UserPhoneMotherObject::create()->phoneAsString(),
            ],
            "Invalid phone" => [
                "errorMessage" => "The number dwadad has an invalid format",
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
        $userRegistered = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($userRegistered);

        $response = $this->post(
            \route(
                'api.register',
                [
                    "email" => $userRegistered->email()->value(),
                    "password" => $this->user->password()->value(),
                    "name" => $this->user->name(),
                    "surname" => $this->user->surname(),
                    "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                    "phone" => $this->user->phone()->phoneAsString(),
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf("The email %s already exists", $userRegistered->email()->value()),
            $decodedResponse["response"]
        );
    }

    public function testItRegistersUser():void
    {
        $response = $this->post(
            \route(
                'api.register',
                [
                    "email" => $this->user->email()->value(),
                    "password" => $this->user->password()->value(),
                    "name" => $this->user->name(),
                    "surname" => $this->user->surname(),
                    "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                    "phone" => $this->user->phone()->phoneAsString(),
                ]
            )
        );

        $expectedUser = $this->userRepository->findByEmailAndPassword(
            $this->user->email(),
            UserPasswordMotherObject::create(
                \md5($this->user->password()->value())
            )
        );

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertTrue($this->user->email()->equals($expectedUser->email()));
        $this->assertTrue($expectedUser->password()->equals(
            UserPassword::fromString(
                \md5($this->userPassword->value())
            )
        ));
    }
}
