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

final class LoginControllerTest extends TestCase
{
    private User $user;
    private string $userPassword;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);

        $this->prepareSavedUser($this->userRepository);
    }

    private function prepareSavedUser(UserRepository $userRepository):void
    {
        $this->userPassword = "1234";

        $this->user = UserMotherObject::createWithoutToken(
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

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
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

        $decodedResponse = $this->decodeResponse($response->getContent());
        $userLogged = $this->userRepository->findByEmailAndPassword(
            $this->user->email(),
            $this->user->password()
        );

        $this->assertSame(Response::HTTP_OK, $response->status());
        $this->assertSame(
            [
                "response" => [
                    "user" => [
                        "id" => $userLogged->id()->value(),
                        "email" => $userLogged->email()->value(),
                        "name" => $userLogged->name(),
                        "surname" => $userLogged->surname(),
                        "bio" => $userLogged->bio(),
                        "location" => $userLogged->location(),
                        "phone_prefix" => $userLogged->phone()->phonePrefixAsString(),
                        "phone" => $userLogged->phone()->phoneAsString(),
                        "picture" => $userLogged->picture(),
                        "show_reviews" => $userLogged->showReviews(),
                        "rating" => $userLogged->rating(),
                    ],
                    "auth" => [
                        "token" => $userLogged->authToken()->value(),
                        "type" => $userLogged->authToken()->type()
                    ],
                ],
                "status" => Response::HTTP_OK
            ],
            $decodedResponse
        );
    }
}
