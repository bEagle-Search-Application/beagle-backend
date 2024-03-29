<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\Logout;

use Beagle\Core\Application\Command\User\Logout\Logout;
use Beagle\Core\Application\Command\User\Logout\LogoutCommand;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalAccessTokenNotFound;
use Beagle\Core\Domain\PersonalToken\Errors\PersonalRefreshTokenNotFound;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryPersonalRefreshTokenRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class LogoutTest extends TestCase
{
    private User $user;
    private UserRepository $userRepository;
    private InMemoryPersonalAccessTokenRepository $personalAccessTokenRepository;
    private InMemoryPersonalRefreshTokenRepository $personalRefreshTokenRepository;
    private Logout $sut;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->personalAccessTokenRepository = new InMemoryPersonalAccessTokenRepository();
        $this->personalRefreshTokenRepository = new InMemoryPersonalRefreshTokenRepository();

        $this->sut = new Logout(
            $this->personalAccessTokenRepository,
            $this->personalRefreshTokenRepository,
            $this->userRepository,
        );
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new LogoutCommand(UserIdMotherObject::create()->value())
        );
    }

    public function testItUserLogouts():void
    {
        $this->prepareDatabase();
        $userId = $this->user->id();

        $this->sut->__invoke(
            new LogoutCommand($userId->value())
        );

        $this->expectException(PersonalAccessTokenNotFound::class);
        $this->personalAccessTokenRepository->findByUserId($userId);

        $this->expectException(PersonalRefreshTokenNotFound::class);
        $this->personalRefreshTokenRepository->findByUserId($userId);
    }

    private function prepareDatabase():void
    {
        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $personalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $this->user->id()
        );
        $this->personalAccessTokenRepository->save($personalAccessToken);

        $personalRefreshToken = PersonalTokenMotherObject::createPersonalRefreshToken(
            userId: $this->user->id()
        );
        $this->personalRefreshTokenRepository->save($personalRefreshToken);
    }
}
