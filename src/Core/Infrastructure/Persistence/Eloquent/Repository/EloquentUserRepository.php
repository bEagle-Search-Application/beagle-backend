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
            ['email', $userEmail->value()],
            ['password', $userPassword->value()],
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
                ['id' => $user->id()->value()],
                [
                    'email' => $user->email()->value(),
                    'password' => $user->password()->value(),
                    'name' => $user->name(),
                    'surname' => $user->surname(),
                    'bio' => $user->bio(),
                    'location' => $user->location(),
                    'phone_prefix' => $user->phone()->phonePrefixAsString(),
                    'phone' => $user->phone()->phoneAsString(),
                    'picture' => $user->picture(),
                    'show_reviews' => $user->showReviews(),
                    'rating' => $user->rating(),
                    'is_verified' => $user->isVerified(),
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
        $userDao = UserDao::where('email', $email->value())->first();

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
        $userDao = UserDao::where('id', $userId->value())->first();

        if ($userDao === null) {
            throw UserNotFound::byId($userId);
        }

        return $this->userDataTransformer->fromDao($userDao);
    }
}
