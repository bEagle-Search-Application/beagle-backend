<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Query\User\Login;

use Beagle\Core\Application\Query\User\Login\Login;
use Beagle\Core\Application\Query\User\Login\LoginQuery;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryPersonalAccessTokenRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryPersonalRefreshTokenRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\IdMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;

final class LoginTest extends TestCase
{
    private Login $sut;
    private User $user;
    private string $userPasswordWithoutHashed;
    private InMemoryPersonalAccessTokenRepository $personalAccessTokenRepository;
    private InMemoryPersonalRefreshTokenRepository $personalRefreshTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $userRepository = new InMemoryUserRepository();
        $this->personalAccessTokenRepository = new InMemoryPersonalAccessTokenRepository();
        $this->personalRefreshTokenRepository = new InMemoryPersonalRefreshTokenRepository();
        $tokenService = new JwtTokenService();

        $this->prepareSavedUser($userRepository);

        $this->sut = new Login(
            $userRepository,
            $tokenService,
            $this->personalAccessTokenRepository,
            $this->personalRefreshTokenRepository
        );
    }

    private function prepareSavedUser(UserRepository $userRepository):void
    {
        $this->userPasswordWithoutHashed = "1234";

        $this->user = UserMotherObject::create(
            userPassword: UserPasswordMotherObject::create($this->userPasswordWithoutHashed)
        );
        $userRepository->save($this->user);
    }

    public function testItThrowsUserNotFoundException():void
    {
        $this->expectException(UserNotFound::class);

        $this->sut->__invoke(
            new LoginQuery(
                IdMotherObject::create()->value(),
                IdMotherObject::create()->value(),
                UserEmailMotherObject::create()->value(),
                UserPasswordMotherObject::create()->value()
            )
        );
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new LoginQuery(
                IdMotherObject::create()->value(),
                IdMotherObject::create()->value(),
                "dani@nomail",
                UserPasswordMotherObject::create()->value()
            )
        );
    }

    public function testItUserLogin():void
    {
        $accessTokenId = IdMotherObject::create();
        $refreshTokenId = IdMotherObject::create();

        $response = $this->sut->__invoke(
            new LoginQuery(
                $accessTokenId->value(),
                $refreshTokenId->value(),
                $this->user->email()->value(),
                \md5($this->userPasswordWithoutHashed)
            )
        );

        $personalAccessToken = $this->personalAccessTokenRepository->findByUserId($this->user->id());
        $personalRefreshToken = $this->personalRefreshTokenRepository->findByUserId($this->user->id());

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
                    "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                    "phone" => $this->user->phone()->phoneAsString(),
                    "picture" => $this->user->picture(),
                    "show_reviews" => $this->user->showReviews(),
                    "rating" => $this->user->rating(),
                    "is_verified" => $this->user->isVerified()
                ],
                "auth" => [
                    "access_token" => $personalAccessToken->token()->value(),
                    "refresh_token" => $personalRefreshToken->token()->value()
                ],
            ]
        );
    }
}
