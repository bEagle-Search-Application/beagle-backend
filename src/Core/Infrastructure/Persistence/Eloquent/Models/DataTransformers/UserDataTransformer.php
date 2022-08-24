<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserDao;
use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\Errors\InvalidPassword;

final class UserDataTransformer
{
    /**
     * @throws InvalidEmail
     * @throws InvalidPassword
     */
    public function fromDao(UserDao $userDao): User
    {
        return new User(
            UserId::fromString($userDao->id),
            UserEmail::fromString($userDao->email),
            UserPassword::fromString($userDao->password),
            $userDao->name,
            $userDao->surname,
            $userDao->bio,
            $userDao->location,
            $userDao->phone,
            $userDao->picture,
            (bool) $userDao->show_reviews,
            $userDao->rating,
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
        $userDao->phone = $user->phone();
        $userDao->picture = $user->picture();
        $userDao->show_reviews = $user->showReviews();
        $userDao->rating = $user->rating();

        return $userDao;
    }
}
