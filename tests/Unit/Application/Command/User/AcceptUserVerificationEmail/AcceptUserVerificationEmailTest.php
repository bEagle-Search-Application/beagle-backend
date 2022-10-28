<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\AcceptUserVerificationEmail;

use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmail;
use Beagle\Core\Application\Command\User\AcceptUserVerificationEmail\AcceptUserVerificationEmailCommand;
use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\UserVerificationToken;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserVerificationTokenRepository;
use Beagle\Shared\Domain\Errors\TokenExpired;
use Beagle\Shared\Infrastructure\Token\JwtTokenService;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\TokenMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\UserVerificationTokenMotherObject;

final class AcceptUserVerificationEmailTest extends TestCase
{
    private AcceptUserVerificationEmail $sut;
    private User $user;
    private UserRepository $userRepository;
    private UserVerificationToken $userVerification;
    private InMemoryUserVerificationTokenRepository $userVerificationTokenRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->userVerificationTokenRepository = new InMemoryUserVerificationTokenRepository();
        $tokenService = new JwtTokenService();

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->userVerification = UserVerificationTokenMotherObject::create(
            userId: $this->user->id()
        );
        $this->userVerificationTokenRepository->save($this->userVerification);

        $this->sut = new AcceptUserVerificationEmail(
            $this->userRepository,
            $this->userVerificationTokenRepository,
            $tokenService
        );
    }

    public function testItThrowsUserVerificationNotFoundException():void
    {
        $this->expectException(UserVerificationNotFound::class);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                TokenMotherObject::createAccessToken()->value()
            )
        );
    }

    public function testItThrowsInvalidTokenExceptionIfUserVerificationExpired():void
    {
        $this->expectException(TokenExpired::class);

        $expiredUserVerification = UserVerificationTokenMotherObject::createExpiredAccessToken(
            userId: $this->user->id()
        );
        $this->userVerificationTokenRepository->save($expiredUserVerification);

        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $expiredUserVerification->token()->value()
            )
        );
    }

    public function testItUserVerifiesEmail():void
    {
        $this->sut->__invoke(
            new AcceptUserVerificationEmailCommand(
                $this->userVerification->token()->value()
            )
        );

        $expectedUserVerification = $this->userVerificationTokenRepository->findByUserId(
            $this->userVerification->userId()
        );
        $expectedUser = $this->userRepository->findByEmail(
            $this->user->email()
        );

        $this->assertTrue($expectedUserVerification->token()->equals($this->userVerification->token()));
        $this->assertTrue($expectedUserVerification->userId()->equals($this->userVerification->userId()));
        $this->assertTrue($expectedUser->isVerified());
    }
}
