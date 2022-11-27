<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalRefreshTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\TestCase;

final class LoginControllerTest extends TestCase
{
    private User $user;
    private string $userPasswordWithoutHash;
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $personalAccessTokenRepository;
    private PersonalRefreshTokenRepository $personalRefreshTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);
        $this->personalRefreshTokenRepository = $this->app->make(EloquentPersonalRefreshTokenRepository::class);

        $this->prepareSavedUser($this->userRepository);
    }

    private function prepareSavedUser(UserRepository $userRepository):void
    {
        $this->userPasswordWithoutHash = "1234";

        $this->user = UserMotherObject::create(
            userPassword: UserPasswordMotherObject::create($this->userPasswordWithoutHash)
        );
        $userRepository->save($this->user);
    }

    public function testItReturnsBadRequestResponseIfCredentialsAreIncorrect():void
    {
        $userEmail = UserEmailMotherObject::create()->value();

        $response = $this->post(
            \route('api.login'),
            [
                "email" => $userEmail,
                "password" => StringMotherObject::createNumber(),
            ]
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf("Las credenciales de %s son incorrectas", $userEmail),
            $decodedResponse["response"]
        );
    }

    public function testItUserLogin():void
    {
        $response = $this->post(
            \route('api.login'),
            [
                "email" => $this->user->email()->value(),
                "password" => $this->userPasswordWithoutHash,
            ]
        );

        $userLogged = $this->userRepository->findByEmailAndPassword(
            $this->user->email(),
            $this->user->password()
        );

        $personalAccessToken = $this->personalAccessTokenRepository->findByUserId($userLogged->id());
        $personalRefreshToken = $this->personalRefreshTokenRepository->findByUserId($userLogged->id());

        $this->assertSame(Response::HTTP_OK, $response->status());
        $response->assertExactJson(
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
                        "is_verified" => $userLogged->isVerified()
                    ],
                    "auth" => [
                        "access_token" => $personalAccessToken->token()->value(),
                        "refresh_token" => $personalRefreshToken->token()->value()
                    ],
                ],
                "status" => Response::HTTP_OK
            ]
        );
    }
}
