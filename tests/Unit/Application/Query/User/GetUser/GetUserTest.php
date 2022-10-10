<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Query\User\GetUser;

use Beagle\Core\Application\Query\User\GetUser\GetUser;
use Beagle\Core\Application\Query\User\GetUser\GetUserQuery;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class GetUserTest extends TestCase
{
    private GetUser $sut;
    private InMemoryUserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();

        $this->sut = new GetUser($this->userRepository);
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new GetUserQuery(UserIdMotherObject::create()->value())
        );
    }

    public function testItGetsUser():void
    {
        $user = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($user);

        $response = $this->sut->__invoke(
            new GetUserQuery($user->id()->value())
        );

        $this->assertSame(
            [
                "id" => $user->id()->value(),
                "email" => $user->email()->value(),
                "name" => $user->name(),
                "surname" => $user->surname(),
                "bio" => $user->bio(),
                "location" => $user->location(),
                "phone_prefix" => $user->phone()->phonePrefixAsString(),
                "phone" => $user->phone()->phoneAsString(),
                "picture" => $user->picture(),
                "show_reviews" => $user->showReviews(),
                "rating" => $user->rating(),
                "is_verified" => $user->isVerified()
            ],
            $response->toArray()
        );
    }
}
