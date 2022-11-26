<?php declare(strict_types = 1);

namespace Tests\Integration\Infrastructure\Http\Api\Controllers;

use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\EloquentUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\MotherObjects\BooleanMotherObject;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\PhoneMotherObject;
use Tests\MotherObjects\PhonePrefixMotherObject;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPhoneMotherObject;
use Tests\TestCase;

final class EditUserControllerTest extends TestCase
{
    private User $user;
    private PersonalAccessToken $personalAccessToken;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(EloquentUserRepository::class);
        $personalAccessTokenRepository = $this->app->make(EloquentPersonalAccessTokenRepository::class);

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->personalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $this->user->id()
        );
        $personalAccessTokenRepository->save($this->personalAccessToken);
    }

    /** @dataProvider invalidArgumentsProvider */
    public function testItReturnsBadRequestResponseIfArgumentsAreInvalid(
        string $email,
        string $phonePrefix,
        string $phone,
    ):void {
        $response = $this->put(
            \route(
                'api.edit-user',
                [
                    'userId' => UserIdMotherObject::create()->value(),
                ]
            ),
            [
                'email' => $email,
                'name' => StringMotherObject::createName(),
                'surname' => StringMotherObject::createSurname(),
                'phone_prefix' => $phonePrefix,
                'phone' => $phone,
                'location' => StringMotherObject::createLocation(),
                'bio' => StringMotherObject::create(),
                'show_reviews' => BooleanMotherObject::create(),
            ],
            [
                'authorization' => "Bearer " . $this->personalAccessToken->token()->value()
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function invalidArgumentsProvider():array
    {
        return [
            "Invalid email" => [
                "email" => "dani@@@",
                "phonePrefix" => PhonePrefixMotherObject::create()->value(),
                "phone" => PhoneMotherObject::create()->value()
            ],
            "Invalid phone prefix" => [
                "email" => UserEmailMotherObject::create()->value(),
                "phonePrefix" => "+45544",
                "phone" => PhoneMotherObject::create()->value()
            ],
            "Invalid phone" => [
                "email" => UserEmailMotherObject::create()->value(),
                "phonePrefix" => PhonePrefixMotherObject::create()->value(),
                "phone" => "dfdssd"
            ],
        ];
    }

    public function testItReturnsForbiddenResponseIfUserHasNotEnoughPermissionsToEditUserInformation():void
    {
        $response = $this->put(
            \route(
                'api.edit-user',
                [
                    'userId' => UserIdMotherObject::create()->value(),
                ]
            ),
            [
                'email' => UserEmailMotherObject::create()->value(),
                'name' => StringMotherObject::createName(),
                'surname' => StringMotherObject::createSurname(),
                'phone_prefix' => PhonePrefixMotherObject::create()->value(),
                'phone' => PhoneMotherObject::create()->value(),
                'location' => StringMotherObject::createLocation(),
                'bio' => StringMotherObject::create(),
                'show_reviews' => BooleanMotherObject::create(),
            ],
            [
                'authorization' => "Bearer " . $this->personalAccessToken->token()->value()
            ]
        );

        $responseArray = $response->decodeResponseJson();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertSame(
            \sprintf(
                'The user %s cannot edit this user information',
                $this->user->id()->value()
            ),
            $responseArray['response']
        );
    }

    public function testItUserHasBeenEdited():void
    {
        $userEmail = UserEmailMotherObject::create();
        $name = StringMotherObject::createName();
        $surname = StringMotherObject::createSurname();
        $userPhone = UserPhoneMotherObject::create(
            PhonePrefixMotherObject::create(),
            PhoneMotherObject::create()
        );
        $location = StringMotherObject::createLocation();
        $bio = StringMotherObject::create();
        $showReviews = BooleanMotherObject::create();

        $response = $this->put(
            \route(
                'api.edit-user',
                [
                    'userId' => $this->user->id()->value(),
                ]
            ),
            [
                'email' => $userEmail->value(),
                'name' => $name,
                'surname' => $surname,
                'phone_prefix' => $userPhone->phonePrefixAsString(),
                'phone' => $userPhone->phoneAsString(),
                'location' => $location,
                'bio' => $bio,
                'show_reviews' => $showReviews,
            ],
            [
                'authorization' => "Bearer " . $this->personalAccessToken->token()->value()
            ]
        );

        $user = $this->userRepository->find($this->user->id());

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertTrue($user->email()->equals($userEmail));
        $this->assertSame($user->name(), $name);
        $this->assertSame($user->surname(), $surname);
        $this->assertTrue($user->phone()->equals($userPhone));
        $this->assertSame($user->location(), $location);
        $this->assertSame($user->bio(), $bio);
        $this->assertSame($user->showReviews(), $showReviews);
    }
}
