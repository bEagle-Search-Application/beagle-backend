<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\RefreshToken;

use Beagle\Core\Application\Command\User\RefreshToken\RefreshToken;
use Beagle\Core\Application\Command\User\RefreshToken\RefreshTokenCommand;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\PersonalToken\PersonalTokenIdMotherObject;
use Tests\MotherObjects\PersonalToken\PersonalTokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;

final class RefreshTokenTest extends TestCase
{
    private RefreshToken $sut;
    private User $user;
    private InMemoryPersonalAccessTokenRepository $personalAccessTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $tokenService = new JwtTokenService();
        $this->personalAccessTokenRepository = new InMemoryPersonalAccessTokenRepository();
        $userRepository = new InMemoryUserRepository();

        $this->user = UserMotherObject::createWithHashedPassword();
        $userRepository->save($this->user);

        $this->sut = new RefreshToken(
            $tokenService,
            $this->personalAccessTokenRepository,
            $userRepository
        );
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new RefreshTokenCommand(
                UserIdMotherObject::create()->value(),
                PersonalTokenIdMotherObject::create()->value()
            )
        );
    }

    public function testItUpdatesAccessToken():void
    {
        $oldPersonalAccessToken = PersonalTokenMotherObject::createPersonalAccessToken(
            userId: $this->user->id()
        );
        $this->personalAccessTokenRepository->save($oldPersonalAccessToken);

        $this->sut->__invoke(
            new RefreshTokenCommand(
                $this->user->id()->value(),
                PersonalTokenIdMotherObject::create()->value()
            )
        );

        $newPersonalAccessToken = $this->personalAccessTokenRepository->findByUserId($this->user->id());

        $this->assertTrue($oldPersonalAccessToken->userId()->equals($newPersonalAccessToken->userId()));
        $this->assertFalse($oldPersonalAccessToken->token()->equals($newPersonalAccessToken->token()));
    }
}
