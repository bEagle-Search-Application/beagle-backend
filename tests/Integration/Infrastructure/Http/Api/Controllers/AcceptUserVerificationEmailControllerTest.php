<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Domain\User\UserVerificationTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserVerificationTokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserVerificationTokenMotherObject;
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

        $this->userVerification = UserVerificationTokenMotherObject::create(userId: $userId);
        $this->userVerificationRepository->save($this->userVerification);
    }

    public function testItReturnsUnauthorizedResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.users-verify',
                [
                    "token" => "ehfoiregierg48743034htkjfnj",
                ]
            )
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testItReturnsBadRequestResponseIfUserVerificationExpired():void
    {
        $expiredUserVerification = UserVerificationTokenMotherObject::createExpiredAccessToken(
            userId: $this->user->id()
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

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
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
