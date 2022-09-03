<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Query\User\Login;

use Beagle\Core\Application\Query\User\Login\Login;
use Beagle\Core\Application\Query\User\Login\LoginQuery;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\UserNotFound;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\TestDoubles\Infrastructure\Auth\AuthServiceMock;

final class LoginTest extends TestCase
{
    private Login $sut;
    private User $user;
    private string $userPassword;

    protected function setUp():void
    {
        parent::setUp();

        $userRepository = new InMemoryUserRepository();
        $authService = new AuthServiceMock($userRepository);

        $this->prepareSavedUser($userRepository);

        $this->sut = new Login(
            $userRepository,
            $authService
        );
    }

    private function prepareSavedUser(UserRepository $userRepository):void
    {
        $this->userPassword = "1234";

        $this->user = UserMotherObject::createWithoutToken(
            userPassword: UserPassword::fromString(
                \md5($this->userPassword)
            )
        );
        $userRepository->save($this->user);
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new LoginQuery(
                UserEmailMotherObject::create()->value(),
                UserPasswordMotherObject::createWithHash()->value()
            )
        );
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new LoginQuery(
                "dani@nomail",
                UserPasswordMotherObject::createWithHash()->value()
            )
        );
    }

    public function testItUserLogin():void
    {
        $response = $this->sut->__invoke(
            new LoginQuery(
                $this->user->email()->value(),
                \md5($this->userPassword)
            )
        );

        $this->assertSame(
            $response->toArray(),
            [
                "user" => [
                    "id" => $this->user->id()->value(),
                    "email" => $this->user->email()->value(),
                    "name" => $this->user->name(),
                    "surname" => $this->user->surname(),
                    "bio" => $this->user->bio(),
                    "location" => $this->user->location(),
                    "phone" => $this->user->phone(),
                    "picture" => $this->user->picture(),
                    "show_reviews" => $this->user->showReviews(),
                    "rating" => $this->user->rating(),
                ],
                "auth" => [
                    "token" => "jhdguferf87er6g87reg68er",
                    "type" => "Bearer",
                ],
            ]
        );
    }
}
