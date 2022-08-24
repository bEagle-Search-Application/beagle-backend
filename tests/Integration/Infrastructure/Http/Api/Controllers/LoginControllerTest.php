<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Shared\Infrastructure\Http\Api\HttpErrorCode;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\TestCase;

final class LoginControllerTest extends TestCase
{
    private User $user;
    private string $userPassword;

    protected function setUp():void
    {
        parent::setUp();

        $userRepository = $this->app->make(EloquentUserRepository::class);

        $this->prepareSavedUser($userRepository);
    }

    private function prepareSavedUser(UserRepository $userRepository):void
    {
        $this->userPassword = "1234";

        $this->user = UserMotherObject::create(
            userPassword: UserPassword::fromString(
                \md5($this->userPassword)
            )
        );
        $userRepository->save($this->user);
    }

    public function testItReturnsBadRequestResponse():void
    {
        $userEmail = UserEmailMotherObject::create()->value();

        $response = $this->get(
            \route(
                'api.login',
                [
                    "email" => $userEmail,
                    "password" => UserPasswordMotherObject::create()->value(),
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(HttpErrorCode::BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf("The credentials for %s are invalid", $userEmail),
            $decodedResponse["response"]
        );
    }

    public function testItUserLogin():void
    {
        $response = $this->get(
            \route(
                'api.login',
                [
                    "email" => $this->user->email()->value(),
                    "password" => $this->userPassword,
                ]
            )
        );

        $this->assertSame(HttpErrorCode::OK, $response->status());
    }
}
