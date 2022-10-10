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
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use ReallySimpleJWT\Token as SimpleJwt;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\DateTimeMotherObject;
use Tests\MotherObjects\PersonalToken\PersonalTokenIdMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class RefreshTokenControllerTest extends TestCase
{
    private JwtTokenService $jwtTokenService;
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $personalAccessTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->jwtTokenService = $this->app->make(JwtTokenService::class);
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

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame(
            "La firma del token de refresco es inválida",
            $decodedResponse["response"]
        );
    }

    public function testItReturnUnauthorizedResponseITokenExpired():void
    {
        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => UserIdMotherObject::create()->value(),
                'exp' => DateTimeMotherObject::yesterday()->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_REFRESH_SECRET')
        );
        $accessToken = Token::accessTokenFromString($token);

        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer " . $accessToken->value()
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnsUnauthorizedExceptionIfTokenNotFound():void
    {
        $userId = UserIdMotherObject::create();
        $token = $this->jwtTokenService->generateRefreshToken($userId);

        $response = $this->post(
            \route(
                'api.token-refresh'
            ),
            headers: [
                'authorization' => "Bearer " . $token->value()
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame(
            \sprintf(
                "No se ha encontrado ningún token de refresco asociado al usuario %s",
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
        $accessToken = $this->jwtTokenService->generateAccessToken($userId);
        $personalAccessToken = new PersonalAccessToken(
            PersonalTokenIdMotherObject::create(),
            $userId,
            $accessToken
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);

        return $personalAccessToken;
    }

    private function prepareRefreshToken(UserId $userId):PersonalRefreshToken
    {
        $refreshToken = $this->jwtTokenService->generateRefreshToken($userId);
        $personalRefreshToken = new PersonalRefreshToken(
            PersonalTokenIdMotherObject::create(),
            $userId,
            $refreshToken
        );
        $this->personalRefreshTokenRepository->save($personalRefreshToken);

        return $personalRefreshToken;
    }
}
