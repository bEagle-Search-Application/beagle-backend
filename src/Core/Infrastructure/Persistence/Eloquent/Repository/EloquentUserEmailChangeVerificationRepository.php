<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\UserEmailChangeVerification;
use Beagle\Core\Domain\User\UserEmailChangeVerificationRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserEmailChangeVerificationDao;

final class EloquentUserEmailChangeVerificationRepository implements UserEmailChangeVerificationRepository
{
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

    public function find(UserId $userId):UserEmailChangeVerification
    {
        throw new \Exception("Not implemented yet");
    }
}
