<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers;

use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserDao;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;

final class UserDataTransformer
{
    /** @throws InvalidValueObject */
    public function fromDao(UserDao $userDao): User
    {
        return new User(
            UserId::fromString($userDao->getAttribute(UserDao::ID)),
            UserEmail::fromString($userDao->getAttribute(UserDao::EMAIL)),
            UserPassword::fromString($userDao->getAttribute(UserDao::PASSWORD)),
            $userDao->getAttribute(UserDao::NAME),
            $userDao->getAttribute(UserDao::SURNAME),
            $userDao->getAttribute(UserDao::BIO),
            $userDao->getAttribute(UserDao::LOCATION),
            UserPhone::create(
                PhonePrefix::fromString($userDao->getAttribute(UserDao::PHONE_PREFIX)),
                Phone::fromString($userDao->getAttribute(UserDao::PHONE)),
            ),
            $userDao->getAttribute(UserDao::PICTURE),
            (bool) $userDao->getAttribute(UserDao::SHOW_REVIEWS),
            $userDao->getAttribute(UserDao::RATING),
            (bool) $userDao->getAttribute(UserDao::IS_VERIFIED),
        );
    }
}
