<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestCase;

final class GetUserControllerTest extends TestCase
{
    private User $user;
    private PersonalAccessToken $personalAccessToken;

    protected function setUp():void
    {
        parent::setUp();

        $userRepository = $this->app->make(EloquentUserRepository::class);
        $personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);

        $this->user = UserMotherObject::createWithHashedPassword();
        $userRepository->save($this->user);

        $this->personalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $this->user->id()
        );
        $personalAccessTokenRepository->save($this->personalAccessToken);
    }

    public function testItReturnsNotFoundResponseIfUserDoesNotExists():void
    {
        $response = $this->get(
            \route(
                'api.get-user',
                [
                    'userId' => UserIdMotherObject::create()->value(),
                ]
            ),
            headers: [
                'authorization' => "Bearer " . $this->personalAccessToken->token()->value()
            ]
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testItReturnsAnUser():void
    {
        $response = $this->get(
            route(
                'api.get-user',
                [
                    'userId' => $this->user->id()->value(),
                ]
            ),
            headers: [
                'authorization' => "Bearer " . $this->personalAccessToken->token()->value()
            ]
        );

        $decodedResponse = $this->decodeResponse($response->getContent());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame(
            [
                "response" => [
                    "id" => $this->user->id()->value(),
                    "email" => $this->user->email()->value(),
                    "name" => $this->user->name(),
                    "surname" => $this->user->surname(),
                    "bio" => $this->user->bio(),
                    "location" => $this->user->location(),
                    "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                    "phone" => $this->user->phone()->phoneAsString(),
                    "picture" => $this->user->picture(),
                    "show_reviews" => $this->user->showReviews(),
                    "rating" => $this->user->rating(),
                    "is_verified" => $this->user->isVerified(),
                ],
                "status" => Response::HTTP_OK,
            ],
            $decodedResponse
        );
    }
}
