<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserEmailChangeVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserEmailChangeVerificationMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserEmailVerificationMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class AcceptUserEmailChangeVerificationEmailControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private UserEmailChangeVerificationRepository $userEmailChangeVerificationRepository;
    private User $user;
    private UserEmailChangeVerification $userEmailChangeVerification;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $this->userEmailChangeVerificationRepository = $this->app->make(
            EloquentUserEmailChangeVerificationRepository::class
        );

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->prepareUserVerification();
    }

    private function prepareUserVerification():void
    {
        $this->userEmailChangeVerification = UserEmailChangeVerificationMotherObject::create(
            $this->user->id(),
            $this->user->email(),
            UserEmailMotherObject::create(),
        );
        $this->userEmailChangeVerificationRepository->save($this->userEmailChangeVerification);
    }

    public function testItReturnsForbiddenResponseIfAuthorAndUserAreNotTheSame():void
    {
        $userVerificationToken = UserEmailVerificationMotherObject::create();
        $userId = UserIdMotherObject::create();

        $response = $this->post(
            \route(
                'api.users-verify-email-change',
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
                "El usuario %s no puede validar esta confirmación de cambio de email",
                $userId->value()
            ),
            $decodedResponse["response"]
        );
    }

    public function testItReturnsUnauthorizedResponseIfTokenIsInvalid():void
    {
        $response = $this->post(
            \route(
                'api.users-verify-email-change',
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
        $token = TokenMotherObject::createExpiredAccessToken(
            userId: $this->user->id()
        );

        $response = $this->post(
            \route(
                'api.users-verify-email-change',
                [
                    "userId" => UserIdMotherObject::create()->value(),
                    "token" => $token->value(),
                ]
            )
        );

        $decodedResponse = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertSame("El token ha caducado", $decodedResponse["response"]);
    }

    public function testItReturnsNoContentResponseIfUserEmailChangeVerifies():void
    {
        $userWithOldEmail = $this->userRepository->find($this->user->id());
        $this->assertTrue($userWithOldEmail->email()->equals($this->user->email()));

        $token = TokenMotherObject::createAccessToken(
            userId: $this->user->id()
        );

        $response = $this->post(
            \route(
                'api.users-verify-email-change',
                [
                    "userId" => $this->user->id()->value(),
                    "token" => $token->value(),
                ]
            )
        );

        $expectedUserWithNewEmail = $this->userRepository->find($this->user->id());
        $expectedUserEmailChangeVerification = $this->userEmailChangeVerificationRepository->find($this->user->id());

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->status());
        $this->assertTrue($expectedUserEmailChangeVerification->confirmed());
        $this->assertTrue($expectedUserEmailChangeVerification->newEmail()->equals($expectedUserWithNewEmail->email()));
    }
}
