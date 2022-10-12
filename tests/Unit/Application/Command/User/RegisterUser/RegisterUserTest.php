<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\RegisterUser;

use Beagle\Core\Application\Command\User\RegisterUser\RegisterUser;
use Beagle\Core\Application\Command\User\RegisterUser\RegisterUserCommand;
use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Event\UserCreated;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPhone;
use Beagle\Shared\Domain\Errors\InvalidPhonePrefix;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\PhoneMotherObject;
use Tests\MotherObjects\PhonePrefixMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\TestDoubles\Bus\EventBusSpy;

final class RegisterUserTest extends TestCase
{
    private RegisterUser $sut;
    private User $user;
    private UserRepository $userRepository;
    private EventBusSpy $eventBus;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();
        $this->eventBus = new EventBusSpy();

        $this->user = UserMotherObject::createForRegister(
            userPassword: UserPasswordMotherObject::createWithHash()
        );

        $this->sut = new RegisterUser(
            $this->userRepository,
            $this->eventBus
        );
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
                $this->user->phone()->phonePrefixAsString(),
                $this->user->phone()->phoneAsString(),
            )
        );
    }

    /** @dataProvider invalidPhonesProvider */
    public function testItThrowsExceptionWhenPhoneIsInvalid(
        string $phoneNumber,
        string $phonePrefix,
        string $exception
    ):void {
        $this->expectException($exception);

        $this->sut->__invoke(
            new RegisterUserCommand(
                $this->user->id()->value(),
                $this->user->email()->value(),
                $this->user->password()->value(),
                $this->user->name(),
                $this->user->surname(),
                $phonePrefix,
                $phoneNumber,
            )
        );
    }

    public function invalidPhonesProvider():array
    {
        return [
            "Invalid phone number" => [
                "phoneNumber" => "a",
                "phonePrefix" => PhonePrefixMotherObject::create()->value(),
                "exception" => InvalidPhone::class
            ],
            "Invalid phone prefix" => [
                "phoneNumber" => PhoneMotherObject::create()->value(),
                "phonePrefix" => "4654676",
                "exception" => InvalidPhonePrefix::class
            ]
        ];
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
                $this->user->phone()->phonePrefixAsString(),
                $this->user->phone()->phoneAsString(),
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
                $this->user->phone()->phonePrefixAsString(),
                $this->user->phone()->phoneAsString(),
            )
        );

        $expectedUser = $this->userRepository->findByEmailAndPassword(
            $this->user->email(),
            $this->user->password()
        );

        $this->assertEquals($expectedUser, $this->user);
        $this->assertTrue($this->eventBus->eventDispatched(UserCreated::class));
    }
}
