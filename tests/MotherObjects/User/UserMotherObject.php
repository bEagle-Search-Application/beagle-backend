<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Tests\MotherObjects\BooleanMotherObject;
use Tests\MotherObjects\IntegerMotherObject;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPhoneMotherObject;

final class UserMotherObject
{
    /** @throws InvalidValueObject */
    public static function create(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $bio = null,
        ?string $location = null,
        ?string $phone = null,
        ?string $picture = null,
        ?bool $showReviews = null,
        ?int $rating = null,
        ?bool $isVerified = null,
    ):User {
        return new User(
            $userId ?? UserIdMotherObject::create(),
            $userEmail ?? UserEmailMotherObject::create(),
            $userPassword ?? UserPasswordMotherObject::create(),
            $name ?? StringMotherObject::createName(),
            $surname ?? StringMotherObject::createSurname(),
            $bio ?? StringMotherObject::create(),
            $location ?? StringMotherObject::createLocation(),
            $phone ?? UserPhoneMotherObject::create(),
            $picture ?? StringMotherObject::createPath(),
            $showReviews ?? BooleanMotherObject::create(),
            $rating ?? IntegerMotherObject::createRating(),
            $isVerified ?? BooleanMotherObject::create(),
        );
    }

    public static function createWithoutVerification(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $bio = null,
        ?string $location = null,
        ?string $phone = null,
        ?string $picture = null,
        ?bool $showReviews = null,
        ?int $rating = null,
    ):User {
        return new User(
            $userId ?? UserIdMotherObject::create(),
            $userEmail ?? UserEmailMotherObject::create(),
            $userPassword ?? UserPasswordMotherObject::create(),
            $name ?? StringMotherObject::createName(),
            $surname ?? StringMotherObject::createSurname(),
            $bio ?? StringMotherObject::create(),
            $location ?? StringMotherObject::createLocation(),
            $phone ?? UserPhoneMotherObject::create(),
            $picture ?? StringMotherObject::createPath(),
            $showReviews ?? BooleanMotherObject::create(),
            $rating ?? IntegerMotherObject::createRating(),
            false,
        );
    }

    public static function createWithVerification(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $bio = null,
        ?string $location = null,
        ?string $phone = null,
        ?string $picture = null,
        ?bool $showReviews = null,
        ?int $rating = null,
    ):User {
        return new User(
            $userId ?? UserIdMotherObject::create(),
            $userEmail ?? UserEmailMotherObject::create(),
            $userPassword ?? UserPasswordMotherObject::create(),
            $name ?? StringMotherObject::createName(),
            $surname ?? StringMotherObject::createSurname(),
            $bio ?? StringMotherObject::create(),
            $location ?? StringMotherObject::createLocation(),
            $phone ?? UserPhoneMotherObject::create(),
            $picture ?? StringMotherObject::createPath(),
            $showReviews ?? BooleanMotherObject::create(),
            $rating ?? IntegerMotherObject::createRating(),
            true,
        );
    }

    /** @throws InvalidValueObject */
    public static function createWithHashedPassword(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $bio = null,
        ?string $location = null,
        ?string $phone = null,
        ?string $picture = null,
        ?bool $showReviews = null,
        ?int $rating = null,
        ?bool $isVerified = null,
    ):User {
        return new User(
            $userId ?? UserIdMotherObject::create(),
            $userEmail ?? UserEmailMotherObject::create(),
            $userPassword ?? UserPasswordMotherObject::createWithHash(),
            $name ?? StringMotherObject::createName(),
            $surname ?? StringMotherObject::createSurname(),
            $bio ?? StringMotherObject::create(),
            $location ?? StringMotherObject::createLocation(),
            $phone ?? UserPhoneMotherObject::create(),
            $picture ?? StringMotherObject::createPath(),
            $showReviews ?? BooleanMotherObject::create(),
            $rating ?? IntegerMotherObject::createRating(),
            $isVerified ?? BooleanMotherObject::create(),
        );
    }

    /** @throws InvalidValueObject */
    public static function createForRegister(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $phone = null
    ):User {
        return new User(
            $userId ?? UserIdMotherObject::create(),
            $userEmail ?? UserEmailMotherObject::create(),
            $userPassword ?? UserPasswordMotherObject::createWithHash(),
            $name ?? StringMotherObject::createName(),
            $surname ?? StringMotherObject::createSurname(),
            null,
            null,
            $phone ?? UserPhoneMotherObject::create(),
            null,
            true,
            0,
            false,
        );
    }
}
