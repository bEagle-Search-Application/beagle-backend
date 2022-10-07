<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalAccessTokenRepository;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshTokenRepository;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
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

final class LogoutControllerTest extends TestCase
{
    private User $user;
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $personalAccessTokenRepository;
    private PersonalRefreshTokenRepository $personalRefreshTokenRepository;
    private JwtTokenService $jwtTokenService;
    private Token $accessToken;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);
        $this->personalRefreshTokenRepository = $this->app->make(EloquentPersonalRefreshTokenRepository::class);
        $this->jwtTokenService = $this->app->make(JwtTokenService::class);
    }

    public function testItReturnUnauthorizedResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.logout'
            ),
            [],
            [
                'authorization' => "Bearer dasdasxasasxcdscsdcdsdcdscds"
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame(
            "La firma del token de acceso es inválida",
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
            \env('JWT_ACCESS_SECRET')
        );
        $accessToken = Token::accessTokenFromString($token);

        $response = $this->post(
            \route(
                'api.logout'
            ),
            [],
            [
                'authorization' => "Bearer " . $accessToken->value()
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnUnauthorizedResponseIfUserTokenNotFound():void
    {
        $userId = UserIdMotherObject::create();
        $accessToken = Token::accessTokenFromString(
            $this->jwtTokenService->generateAccessToken(
                $userId
            )->value()
        );

        $response = $this->post(
            \route(
                'api.logout'
            ),
            [],
            [
                'authorization' => "Bearer " . $accessToken->value()
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame(
            \sprintf("No se ha encontrado ningún token de acceso asociado al usuario %s", $userId->value()),
            $decodedResponse["response"]
        );
    }

    public function testItUserLogout():void
    {
        $this->prepareDatabase($this->userRepository);
        $userId = $this->user->id();

        $accessToken = Token::accessTokenFromString(
            $this->jwtTokenService->generateAccessToken(
                $userId
            )->value()
        );

        $response = $this->post(
            \route(
                'api.logout'
            ),
            [],
            [
                'authorization' => "Bearer " . $accessToken->value()
            ]
        );

        $this->expectException(PersonalAccessTokenNotFound::class);
        $this->personalAccessTokenRepository->findByUserId($userId);

        $this->expectException(PersonalRefreshTokenNotFound::class);
        $this->personalRefreshTokenRepository->findByUserId($userId);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
    }

    private function prepareDatabase(UserRepository $userRepository):void
    {
        $this->user = UserMotherObject::createWithHashedPassword();
        $userRepository->save($this->user);

        $this->accessToken = Token::accessTokenFromString(
            $this->jwtTokenService->generateAccessToken(
                $this->user->id()
            )->value()
        );
        $personalAccessToken = new PersonalAccessToken(
            PersonalTokenIdMotherObject::create(),
            $this->user->id(),
            $this->accessToken
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);

        $refreshToken = Token::refreshTokenFromString(
            $this->jwtTokenService->generateRefreshToken(
                $this->user->id()
            )->value()
        );
        $personalRefreshToken = new PersonalRefreshToken(
            PersonalTokenIdMotherObject::create(),
            $this->user->id(),
            $refreshToken
        );
        $this->personalRefreshTokenRepository->save($personalRefreshToken);
    }
}
