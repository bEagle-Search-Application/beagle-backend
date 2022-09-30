<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\RefreshToken;

use Beagle\Core\Application\Command\User\RefreshToken\RefreshToken;
use Beagle\Core\Application\Command\User\RefreshToken\RefreshTokenCommand;
use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\TokenType;
use Beagle\Shared\Domain\ValueObjects\Token;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\DateTimeMotherObject;
use Tests\MotherObjects\PersonalToken\PersonalTokenIdMotherObject;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\TestDoubles\Infrastructure\Auth\TokenServiceMock;

final class RefreshTokenTest extends TestCase
{
    private RefreshToken $sut;
    private User $user;
    private InMemoryPersonalAccessTokenRepository $personalAccessTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $tokenService = new TokenServiceMock();
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
        $oldAccessToken = TokenMotherObject::customize(
            TokenType::ACCESS,
            $this->user->id(),
            DateTimeMotherObject::yesterday()
        );
        $oldPersonalAccessToken = new PersonalAccessToken(
            PersonalTokenIdMotherObject::create(),
            $this->user->id(),
            Token::accessTokenFromString($oldAccessToken->value())
        );
        $this->personalAccessTokenRepository->save($oldPersonalAccessToken);

        $refreshToken = TokenMotherObject::customize(
            TokenType::REFRESH,
            $this->user->id(),
            DateTimeMotherObject::tomorrow()
        );

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
