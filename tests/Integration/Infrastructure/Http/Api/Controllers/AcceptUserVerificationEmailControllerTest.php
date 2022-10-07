<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserVerificationTokenRepository;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Token;
use ReallySimpleJWT\Token as SimpleJwt;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\DateTimeMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserVerificationTokenMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserVerificationTokenIdMotherObject;
use Tests\TestCase;

final class AcceptUserVerificationEmailControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private UserVerificationTokenRepository $userVerificationRepository;
    private User $user;
    private UserVerificationToken $userVerification;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->userVerificationRepository = $this->app->make(EloquentUserVerificationTokenRepository::class);

        $this->user = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($this->user);

        $this->prepareUserVerification();
    }

    private function prepareUserVerification():void
    {
        $userId = $this->user->id();
        $token = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $userId,
                'exp' => DateTimeMotherObject::now()->addMinutes(10)->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );

        $this->userVerification = UserVerificationTokenMotherObject::create(
            userId: $userId,
            token: Token::accessTokenFromString($token)
        );
        $this->userVerificationRepository->save($this->userVerification);
    }

    public function testItReturnsNotFoundResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => "ehfoiregierg48743034htkjfnj",
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->status());
        $this->assertSame("La firma del token de acceso es inválida", $decodedResponse["response"]);
    }

    public function testItReturnsBadRequestResponseIfUserVerificationExpired():void
    {
        $expiredToken = SimpleJwt::customPayload(
            [
                'iat' => DateTime::now(),
                'uid' => $this->user->id(),
                'exp' => DateTimeMotherObject::yesterday()->timestamp,
                'iss' => \env('APP_URL')
            ],
            \env('JWT_ACCESS_SECRET')
        );
        $expiredUserVerification = UserVerificationToken::create(
            UserVerificationTokenIdMotherObject::create(),
            $this->user->id(),
            Token::accessTokenFromString($expiredToken)
        );

        $this->userVerificationRepository->save($expiredUserVerification);

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $expiredUserVerification->token()->value(),
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnsNoContentResponseIfUserVerifies():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $this->userVerification->token()->value(),
                ]
            )
        );

        $expectedUserVerified = $this->userRepository->findByEmail($this->user->email());

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertTrue($expectedUserVerified->isVerified());
    }
}
