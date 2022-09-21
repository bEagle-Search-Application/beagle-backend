<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Repository;

use Beagle\Core\Domain\User\Errors\UserVerificationNotFound;
use Beagle\Core\Domain\User\UserVerification;
use Beagle\Core\Domain\User\UserVerificationRepository;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\DataTransformers\UserVerificationDataTransformer;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserVerificationDao;
use Beagle\Shared\Domain\ValueObjects\Email;

final class EloquentUserVerificationRepository implements UserVerificationRepository
{
    public function __construct(private UserVerificationDataTransformer $userVerificationDataTransformer)
    {
    }

    public function save(UserVerification $userVerification):void
    {
        UserVerificationDao::updateOrCreate(
            ['id' => $userVerification->id()->value()],
            [
                'email' => $userVerification->email()->value(),
                'token' => $userVerification->token()->value(),
                'expired_at' => $userVerification->expiredAt()->toDateTimeString()
            ]
        );
    }

    /** @throws UserVerificationNotFound */
    public function findByEmail(Email $email):UserVerification
    {
        $userVerificationDao = UserVerificationDao::where('email', $email->value())->first();

        return $this->userVerificationDataTransformer->fromDao($userVerificationDao);
    }
}
