<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserEmailChangeVerificationNotFound;
use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserEmailChangeVerificationDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserEmailChangeVerificationDao;
use Beagle\Shared\Domain\Errors\InvalidEmail;

final class EloquentUserEmailChangeVerificationRepository implements UserEmailChangeVerificationRepository
{
    public function __construct(
        private UserEmailChangeVerificationDataTransformer $userEmailChangeVerificationDataTransformer
    ) {
    }

    public function save(UserEmailChangeVerification $userChangeEmailVerification):void
    {
        UserEmailChangeVerificationDao::updateOrCreate(
            [UserEmailChangeVerificationDao::USER_ID => $userChangeEmailVerification->userId()->value()],
            [
                UserEmailChangeVerificationDao::OLD_EMAIL => $userChangeEmailVerification->oldEmail()->value(),
                UserEmailChangeVerificationDao::NEW_EMAIL => $userChangeEmailVerification->newEmail()->value(),
                UserEmailChangeVerificationDao::CONFIRMED => $userChangeEmailVerification->confirmed(),
            ]
        );
    }

    /**
     * @throws InvalidEmail
     * @throws UserEmailChangeVerificationNotFound
     */
    public function find(UserId $userId):UserEmailChangeVerification
    {
        $userEmailChangeVerificationDao = UserEmailChangeVerificationDao::where(
            UserEmailChangeVerificationDao::USER_ID,
            $userId->value()
        )->first();

        if ($userEmailChangeVerificationDao === null) {
            throw UserEmailChangeVerificationNotFound::byUserId($userId);
        }

        return $this->userEmailChangeVerificationDataTransformer->fromDao($userEmailChangeVerificationDao);
    }
}
