<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalRefreshTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class RefreshTokenControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $personalAccessTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);
        $this->personalRefreshTokenRepository = $this->app->make(EloquentPersonalRefreshTokenRepository::class);
    }

    public function testItReturnsUnauthorizedExceptionIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer hreibibninbiot4niro"
            ]
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testItReturnUnauthorizedResponseIfTokenExpired():void
    {
        $expiredRefreshToken = TokenMotherObject::createExpiredRefreshToken();

        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer " . $expiredRefreshToken->value()
            ]
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnsForbiddenResponseIfTokenNotFound():void
    {
        $userId = UserIdMotherObject::create();
        $token = TokenMotherObject::createRefreshToken($userId);

        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer " . $token->value()
            ]
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->status());
        $this->assertSame(
            \sprintf(
                "El token especificado no está asociado al usuario %s",
                $userId->value()
            ),
            $decodedResponse["response"]
        );
    }

    public function testItUpdatesAccessToken():void
    {
        $user = UserMotherObject::create();
        $this->userRepository->save($user);

        $userId = $user->id();
        $personalAccessToken = $this->prepareAccessToken($userId);
        $personalRefreshToken = $this->prepareRefreshToken($userId);

        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer " . $personalRefreshToken->token()->value()
            ]
        );

        $updatedPersonalAccessToken = $this->personalAccessTokenRepository->findByUserId($userId);

        $this->assertSame(Response::HTTP_OK, $response->status());
        $this->assertFalse($personalAccessToken->token()->equals($updatedPersonalAccessToken->token()));
    }

    private function prepareAccessToken(UserId $userId):PersonalAccessToken
    {
        $personalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $userId
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);

        return $personalAccessToken;
    }

    private function prepareRefreshToken(UserId $userId):PersonalRefreshToken
    {
        $personalRefreshToken = PersonalTokenMotherObject::createPersonalRefreshToken(
            userId: $userId
        );
        $this->personalRefreshTokenRepository->save($personalRefreshToken);

        return $personalRefreshToken;
    }
}
