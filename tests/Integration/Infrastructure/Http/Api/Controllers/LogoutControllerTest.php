<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalRefreshTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class LogoutControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $personalAccessTokenRepository;
    private PersonalRefreshTokenRepository $personalRefreshTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);
        $this->personalRefreshTokenRepository = $this->app->make(EloquentPersonalRefreshTokenRepository::class);
    }

    public function testItReturnUnauthorizedResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.logout'
            ),
            headers: [
                'authorization' => "Bearer dasdasxasasxcdscsdcdsdcdscds"
            ]
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testItReturnUnauthorizedResponseIfTokenExpired():void
    {
        $expiredAccessToken = TokenMotherObject::createExpiredAccessToken();

        $response = $this->post(
            \route(
                'api.logout'
            ),
            headers: [
                'authorization' => "Bearer " . $expiredAccessToken->value()
            ]
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnForbiddenResponseIfUserTokenNotFound():void
    {
        $userId = UserIdMotherObject::create();
        $accessToken = TokenMotherObject::createAccessToken($userId);

        $response = $this->post(
            \route(
                'api.logout'
            ),
            headers: [
                'authorization' => "Bearer " . $accessToken->value()
            ]
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->status());
        $this->assertSame(
            \sprintf("El token especificado no está asociado al usuario %s", $userId->value()),
            $decodedResponse["response"]
        );
    }

    public function testItUserLogout():void
    {
        $user = UserMotherObject::create();
        $this->userRepository->save($user);

        $userId = $user->id();

        $personalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $userId
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);

        $personalRefreshToken = PersonalTokenMotherObject::createPersonalRefreshToken(
            userId: $userId
        );
        $this->personalRefreshTokenRepository->save($personalRefreshToken);

        $response = $this->post(
            \route(
                'api.logout'
            ),
            headers: [
                'authorization' => "Bearer " . $personalAccessToken->token()->value()
            ]
        );

        $this->expectException(PersonalAccessTokenNotFound::class);
        $this->personalAccessTokenRepository->findByUserId($userId);

        $this->expectException(PersonalRefreshTokenNotFound::class);
        $this->personalRefreshTokenRepository->findByUserId($userId);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
    }
}
