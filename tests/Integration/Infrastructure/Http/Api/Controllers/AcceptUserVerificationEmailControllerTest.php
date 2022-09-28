<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserVerificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserVerificationMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\TestCase;

final class AcceptUserVerificationEmailControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private UserVerificationRepository $userVerificationRepository;
    private User $user;
    private UserVerification $userVerification;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->userVerificationRepository = $this->app->make(EloquentUserVerificationRepository::class);

        $this->user = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($this->user);

        $this->userVerification = UserVerificationMotherObject::create(
            email: $this->user->email()
        );
        $this->userVerificationRepository->save($this->userVerification);
    }

    public function testItReturnsBadRequestResponseIfEmailIsInvalid():void
    {
        $userEmail = "dani@nj";

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $this->userVerification->token()->value(),
                    "email" => $userEmail
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf("El email %s tiene un formato inválido", $userEmail),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsNotFoundResponseIfTokenIsInvalid():void
    {
        $token = TokenMotherObject::create()->value();
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $token,
                    "email" => $this->userVerification->email()->value()
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->status());
        $this->assertSame(
            \sprintf("No se ha encontrado ninguna validación para el token %s", $token),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsBadRequestResponseIfUserVerificationExpired():void
    {
        $expiredUserVerification = UserVerificationMotherObject::createExpired(
            email: $this->user->email()
        );
        $this->userVerificationRepository->save($expiredUserVerification);

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $expiredUserVerification->token()->value(),
                    "email" => $expiredUserVerification->email()->value()
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf(
                "La verificación de usuario ha expirado el %s",
                $expiredUserVerification->expiredAt()->jsonSerialize()
            ),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsBadRequestResponseIfUserEmailNotEqualToUserVerificationEmail():void
    {
        $userEmail = UserEmailMotherObject::create()->value();
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $this->userVerification->token()->value(),
                    "email" => $userEmail
                ]
            )
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->status());
        $this->assertSame(
            \sprintf(
                "El email %s no corresponde con la solicitud de verificación",
                $userEmail
            ),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsNoContentResponseIfUserVerifies():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => $this->userVerification->token()->value(),
                    "email" => $this->userVerification->email()->value()
                ]
            )
        );

        $expectedUserVerified = $this->userRepository->findByEmail($this->user->email());

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertTrue($expectedUserVerified->isVerified());
    }
}
