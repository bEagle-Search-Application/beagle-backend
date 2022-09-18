<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Core\Domain\User\ValueObjects\UserToken;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserDao;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;

final class UserDataTransformer
{
    /** @throws InvalidValueObject */
    public function fromDao(UserDao $userDao): User
    {
        $auth_token = $userDao->auth_token;

        return new User(
            UserId::fromString($userDao->id),
            UserEmail::fromString($userDao->email),
            UserPassword::fromString($userDao->password),
            $userDao->name,
            $userDao->surname,
            $userDao->bio,
            $userDao->location,
            UserPhone::create(
                PhonePrefix::fromString($userDao->phone_prefix),
                Phone::fromString($userDao->phone),
            ),
            $userDao->picture,
            (bool) $userDao->show_reviews,
            $userDao->rating,
            empty($auth_token) ? null :UserToken::fromString($auth_token)
        );
    }

    public function fromUser(User $user): UserDao
    {
        $userDao = new UserDao();

        $userDao->id = $user->id()->value();
        $userDao->email = $user->email()->value();
        $userDao->password = $user->password()->value();
        $userDao->name = $user->name();
        $userDao->surname = $user->surname();
        $userDao->bio = $user->bio();
        $userDao->location = $user->location();
        $userDao->phone_prefix = $user->phone()->phonePrefixAsString();
        $userDao->phone = $user->phone()->phoneAsString();
        $userDao->picture = $user->picture();
        $userDao->show_reviews = $user->showReviews();
        $userDao->rating = $user->rating();
        $userDao->auth_token = $user->authToken();

        return $userDao;
    }
}
