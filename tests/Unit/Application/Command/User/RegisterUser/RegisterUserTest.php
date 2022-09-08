<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\RegisterUser;

use Beagle\Core\Application\Command\User\RegisterUser\RegisterUser;
use Beagle\Core\Application\Command\User\RegisterUser\RegisterUserCommand;
use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;

final class RegisterUserTest extends TestCase
{
    private RegisterUser $sut;
    private User $user;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();

        $this->user = UserMotherObject::createForRegister(
            userPassword: UserPasswordMotherObject::createWithHash()
        );

        $this->sut = new RegisterUser($this->userRepository);
    }

    public function testItThrowsInvalidEmailException():void
    {
        $this->expectException(InvalidEmail::class);

        $this->sut->__invoke(
            new RegisterUserCommand(
                $this->user->id()->value(),
                "dani@nbo",
                $this->user->password()->value(),
                $this->user->name(),
                $this->user->surname(),
                $this->user->bio(),
                $this->user->location(),
                $this->user->phone(),
            )
        );
    }

    public function testItThrowsCannotSaveUserException():void
    {
        $registeredUser = UserMotherObject::createWithHashedPassword();
        $this->userRepository->save($registeredUser);

        $this->expectException(CannotSaveUser::class);

        $this->sut->__invoke(
            new RegisterUserCommand(
                $this->user->id()->value(),
                $registeredUser->email()->value(),
                $this->user->password()->value(),
                $this->user->name(),
                $this->user->surname(),
                $this->user->bio(),
                $this->user->location(),
                $this->user->phone(),
            )
        );
    }

    public function testItRegistersUser():void
    {
        $this->sut->__invoke(
            new RegisterUserCommand(
                $this->user->id()->value(),
                $this->user->email()->value(),
                $this->user->password()->value(),
                $this->user->name(),
                $this->user->surname(),
                $this->user->bio(),
                $this->user->location(),
                $this->user->phone(),
            )
        );

        $expectedUser = $this->userRepository->findByEmailAndPassword(
            $this->user->email(),
            $this->user->password()
        );

        $this->assertEquals($expectedUser, $this->user);
    }
}
