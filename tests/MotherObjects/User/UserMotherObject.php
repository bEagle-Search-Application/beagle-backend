<?php declare(strict_types = 1);

namespace Tests\MotherObjects\User;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Tests\MotherObjects\BooleanMotherObject;
use Tests\MotherObjects\IntegerMotherObject;
use Tests\MotherObjects\StringMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserEmailMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserIdMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserPasswordMotherObject;
use Tests\MotherObjects\User\ValueObjects\UserTokenMotherObject;

final class UserMotherObject
{
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
        ?UserToken $userToken = null
    ):User
    {
        return new User(
            empty($userId) ? UserIdMotherObject::create() : $userId,
            empty($userEmail) ? UserEmailMotherObject::create() : $userEmail,
            empty($userPassword) ? UserPasswordMotherObject::create() : $userPassword,
            empty($name) ? StringMotherObject::createName() : $name,
            empty($surname) ? StringMotherObject::createSurname() : $surname,
            empty($bio) ? StringMotherObject::create() : $bio,
            empty($location) ? StringMotherObject::createLocation() : $location,
            empty($phone) ? StringMotherObject::createPhone() : $phone,
            empty($picture) ? StringMotherObject::createPath() : $picture,
            empty($showReviews) ? BooleanMotherObject::create() : $showReviews,
            empty($rating) ? IntegerMotherObject::createRating() : $rating,
            empty($userToken) ? UserTokenMotherObject::create() : $userToken,
        );
    }

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
        ?UserToken $userToken = null
    ):User
    {
        return new User(
            empty($userId) ? UserIdMotherObject::create() : $userId,
            empty($userEmail) ? UserEmailMotherObject::create() : $userEmail,
            empty($userPassword) ? UserPasswordMotherObject::createWithHash() : $userPassword,
            empty($name) ? StringMotherObject::createName() : $name,
            empty($surname) ? StringMotherObject::createSurname() : $surname,
            empty($bio) ? StringMotherObject::create() : $bio,
            empty($location) ? StringMotherObject::createLocation() : $location,
            empty($phone) ? StringMotherObject::createPhone() : $phone,
            empty($picture) ? StringMotherObject::createPath() : $picture,
            empty($showReviews) ? BooleanMotherObject::create() : $showReviews,
            empty($rating) ? IntegerMotherObject::createRating() : $rating,
            empty($userToken) ? UserTokenMotherObject::create() : $userToken,
        );
    }

    public static function createForRegister(
        ?UserId $userId = null,
        ?UserEmail $userEmail = null,
        ?UserPassword $userPassword = null,
        ?string $name = null,
        ?string $surname = null,
        ?string $bio = null,
        ?string $location = null,
        ?string $phone = null
    ):User
    {
        return new User(
            empty($userId) ? UserIdMotherObject::create() : $userId,
            empty($userEmail) ? UserEmailMotherObject::create() : $userEmail,
            empty($userPassword) ? UserPasswordMotherObject::createWithHash() : $userPassword,
            empty($name) ? StringMotherObject::createName() : $name,
            empty($surname) ? StringMotherObject::createSurname() : $surname,
            empty($bio) ? StringMotherObject::create() : $bio,
            empty($location) ? StringMotherObject::createLocation() : $location,
            empty($phone) ? StringMotherObject::createPhone() : $phone,
            null,
            true,
            0,
            null
        );
    }

    public static function createWithoutToken(
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
    ):User
    {
        return new User(
            empty($userId) ? UserIdMotherObject::create() : $userId,
            empty($userEmail) ? UserEmailMotherObject::create() : $userEmail,
            empty($userPassword) ? UserPasswordMotherObject::create() : $userPassword,
            empty($name) ? StringMotherObject::createName() : $name,
            empty($surname) ? StringMotherObject::createSurname() : $surname,
            empty($bio) ? StringMotherObject::create() : $bio,
            empty($location) ? StringMotherObject::createLocation() : $location,
            empty($phone) ? StringMotherObject::createPhone() : $phone,
            empty($picture) ? StringMotherObject::createPath() : $picture,
            empty($showReviews) ? BooleanMotherObject::create() : $showReviews,
            empty($rating) ? IntegerMotherObject::createRating() : $rating,
            null
        );
    }
}
