<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\CannotSaveUser;
use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\User;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserDao;
use Beagle\Shared\Domain\Errors\InvalidValueObject;
use Illuminate\Database\QueryException;

final class EloquentUserRepository implements UserRepository
{
    public function __construct(private UserDataTransformer $userDataTransformer)
    {
    }

    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function findByEmailAndPassword(UserEmail $userEmail, UserPassword $userPassword):User
    {
        $userDao = UserDao::where([
            [UserDao::EMAIL, $userEmail->value()],
            [UserDao::PASSWORD, $userPassword->value()],
        ])->first();

        if ($userDao === null) {
            throw UserNotFound::byCredentials($userEmail);
        }

        return $this->userDataTransformer->fromDao($userDao);
    }

    /** @throws CannotSaveUser */
    public function save(User $user):void
    {
        try {
            UserDao::updateOrCreate(
                [UserDao::ID => $user->id()->value()],
                [
                    UserDao::EMAIL => $user->email()->value(),
                    UserDao::PASSWORD => $user->password()->value(),
                    UserDao::NAME => $user->name(),
                    UserDao::SURNAME => $user->surname(),
                    UserDao::BIO => $user->bio(),
                    UserDao::LOCATION => $user->location(),
                    UserDao::PHONE_PREFIX => $user->phone()->phonePrefixAsString(),
                    UserDao::PHONE => $user->phone()->phoneAsString(),
                    UserDao::PICTURE => $user->picture(),
                    UserDao::SHOW_REVIEWS => $user->showReviews(),
                    UserDao::RATING => $user->rating(),
                    UserDao::IS_VERIFIED => $user->isVerified(),
                ]
            );
        } catch (QueryException $e) {
            if ($e->getCode() === "23000") {
                throw CannotSaveUser::byEmail($user->email());
            }

            throw $e;
        }
    }

    /**
     * @throws InvalidValueObject
     * @throws UserNotFound
     */
    public function findByEmail(UserEmail $email):User
    {
        $userDao = UserDao::where(UserDao::EMAIL, $email->value())->first();

        if ($userDao === null) {
            throw UserNotFound::byEmail($email);
        }

        return $this->userDataTransformer->fromDao($userDao);
    }

    /**
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    public function find(UserId $userId):User
    {
        $userDao = UserDao::where(UserDao::ID, $userId->value())->first();

        if ($userDao === null) {
            throw UserNotFound::byId($userId);
        }

        return $this->userDataTransformer->fromDao($userDao);
    }
}
