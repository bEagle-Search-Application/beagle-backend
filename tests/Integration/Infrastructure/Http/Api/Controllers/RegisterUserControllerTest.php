<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
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
        string $password
    ):void {
        $response = $this->post(
            \route(
                'api.register',
                [
                    "email" => $email,
                    "password" => $password,
                    "name" => $this->user->name(),
                    "surname" => $this->user->surname(),
                    "phone" => $this->user->phone(),
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
                "password" => UserPasswordMotherObject::create()->value()
            ],
            "Invalid password" => [
                "errorMessage" => "The password must be at least 8 characters.",
                "email" => UserEmailMotherObject::create()->value(),
                "password" => "1234"
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
                    "phone" => $this->user->phone(),
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
                    "phone" => $this->user->phone(),
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
