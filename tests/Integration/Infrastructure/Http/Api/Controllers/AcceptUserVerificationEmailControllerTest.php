<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserEmailVerification;
use Beagle\Core\Domain\User\UserEmailVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserEmailVerificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserEmailVerificationMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class AcceptUserVerificationEmailControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private UserEmailVerificationRepository $userVerificationRepository;
    private User $user;
    private UserEmailVerification $userVerification;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->userVerificationRepository = $this->app->make(EloquentUserEmailVerificationRepository::class);

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->prepareUserVerification();
    }

    private function prepareUserVerification():void
    {
        $userId = $this->user->id();

        $this->userVerification = UserEmailVerificationMotherObject::create(userId: $userId);
        $this->userVerificationRepository->save($this->userVerification);
    }

    public function testItReturnsForbiddenResponseIfAuthorAndUserAreNotTheSame():void
    {
        $userVerificationToken = UserEmailVerificationMotherObject::create();
        $userId = UserIdMotherObject::create();

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "userId" => $userId->value(),
                    "token" => $userVerificationToken->token()->value(),
                ]
            )
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->status());
        $this->assertSame(
            \sprintf(
                "El usuario %s no puede validar este email",
                $userId->value()
            ),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsForbiddenResponseIfUserVerificationDoesNotExists():void
    {
        $userVerificationToken = UserEmailVerificationMotherObject::create();

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "userId" => $userVerificationToken->userId()->value(),
                    "token" => $userVerificationToken->token()->value(),
                ]
            )
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->status());
        $this->assertSame(
            \sprintf(
                "No se ha encontrado ninguna validación para el usuario %s",
                $userVerificationToken->userId()->value()
            ),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsUnauthorizedResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "userId" => UserIdMotherObject::create()->value(),
                    "token" => "ehfoiregierg48743034htkjfnj",
                ]
            )
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testItReturnsUnauthorizedResponseIfUserVerificationExpired():void
    {
        $expiredUserVerification = UserEmailVerificationMotherObject::createExpiredAccessToken(
            userId: $this->user->id()
        );
        $this->userVerificationRepository->save($expiredUserVerification);

        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "userId" => UserIdMotherObject::create()->value(),
                    "token" => $expiredUserVerification->token()->value(),
                ]
            )
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnsNoContentResponseIfUserVerifies():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "userId" => $this->user->id()->value(),
                    "token" => $this->userVerification->token()->value(),
                ]
            )
        );

        $expectedUserVerified = $this->userRepository->findByEmail($this->user->email());

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertTrue($expectedUserVerified->isVerified());
    }
}
