<?php declare(strict_types = 1);

namespace Tests\Unit\Application\Command\User\EditUser;

use Beagle\Core\Application\Command\User\EditUser\EditUser;
use Beagle\Core\Application\Command\User\EditUser\EditUserCommand;
use Beagle\Core\Domain\User\Errors\UserCannotBeEdited;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Infrastructure\Persistence\InMemory\Repository\InMemoryUserRepository;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tests\MotherObjects\BooleanMotherObject;
use Tests\MotherObjects\PhoneMotherObject;
use Tests\MotherObjects\PhonePrefixMotherObject;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\UserMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPhoneMotherObject;

final class EditUserTest extends TestCase
{
    private InMemoryUserRepository $userRepository;
    private User $user;
    private EditUser $sut;

    protected function setUp():void
    {
        parent::setUp();

        $this->userRepository = new InMemoryUserRepository();

        $this->user = UserMotherObject::create();
        $this->userRepository->save($this->user);

        $this->sut = new EditUser($this->userRepository);
    }

    /** @dataProvider invalidArgumentsProvider */
    public function testItThrowsInvalidValueObjectExceptionIfBodyParamsAreInvalid(
        string $email,
        string $phonePrefix,
        string $phone
    ):void {
        $this->expectException(InvalidValueObject::class);

        $this->sut->__invoke(
            new EditUserCommand(
                UserIdMotherObject::create()->value(),
                UserIdMotherObject::create()->value(),
                $email,
                StringMotherObject::createName(),
                StringMotherObject::createSurname(),
                $phonePrefix,
                $phone,
                StringMotherObject::createLocation(),
                StringMotherObject::create(),
                BooleanMotherObject::create()
            )
        );
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

    /** @dataProvider invalidUserIdsProvider */
    public function testItThrowsInvalidArgumentExceptionUserIdsAreInvalid(
        string $authorId,
        string $userId,
    ):void {
        $this->expectException(InvalidArgumentException::class);

        $this->sut->__invoke(
            new EditUserCommand(
                $authorId,
                $userId,
                UserEmailMotherObject::create()->value(),
                StringMotherObject::createName(),
                StringMotherObject::createSurname(),
                PhonePrefixMotherObject::create()->value(),
                PhoneMotherObject::create()->value(),
                StringMotherObject::createLocation(),
                StringMotherObject::create(),
                BooleanMotherObject::create()
            )
        );
    }

    public function invalidUserIdsProvider():array
    {
        return [
            "Invalid author id" => [
                "authorId" => "dsfsdf",
                "userId" => UserIdMotherObject::create()->value(),
            ],
            "Invalid user id" => [
                "authorId" => UserIdMotherObject::create()->value(),
                "userId" => "dgdsf",
            ],
        ];
    }

    public function testItThrowsUserCannotBeEditedExceptionIfAuthorIdIsNotEqualToUserId():void
    {
        $this->expectException(UserCannotBeEdited::class);

        $this->sut->__invoke(
            new EditUserCommand(
                UserIdMotherObject::create()->value(),
                UserIdMotherObject::create()->value(),
                UserEmailMotherObject::create()->value(),
                StringMotherObject::createName(),
                StringMotherObject::createSurname(),
                PhonePrefixMotherObject::create()->value(),
                PhoneMotherObject::create()->value(),
                StringMotherObject::createLocation(),
                StringMotherObject::create(),
                BooleanMotherObject::create()
            )
        );
    }

    public function testItUserHasBeenEdited():void
    {
        $email = UserEmailMotherObject::create();
        $name = StringMotherObject::createName();
        $surname = StringMotherObject::createSurname();
        $userPhone = UserPhoneMotherObject::create(
            PhonePrefixMotherObject::create(),
            PhoneMotherObject::create()
        );
        $location = StringMotherObject::createLocation();
        $bio = StringMotherObject::create();
        $showReviews = BooleanMotherObject::create();

        $this->sut->__invoke(
            new EditUserCommand(
                $this->user->id()->value(),
                $this->user->id()->value(),
                $email->value(),
                $name,
                $surname,
                $userPhone->phonePrefixAsString(),
                $userPhone->phoneAsString(),
                $location,
                $bio,
                $showReviews
            )
        );

        $user = $this->userRepository->find($this->user->id());

        $this->assertTrue($user->email()->equals($email));
        $this->assertSame($user->name(), $name);
        $this->assertSame($user->surname(), $surname);
        $this->assertTrue($user->phone()->equals($userPhone));
        $this->assertSame($user->location(), $location);
        $this->assertSame($user->bio(), $bio);
        $this->assertSame($user->showReviews(), $showReviews);
    }
}
