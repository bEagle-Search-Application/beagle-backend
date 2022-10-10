<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\GetUser;

use Beagle\Core\Domain\User\Errors\UserNotFound;
use Beagle\Core\Domain\User\UserRepository;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Shared\Bus\Query;
use Beagle\Shared\Bus\QueryResponse;
use Beagle\Shared\Domain\Errors\InvalidValueObject;

final class GetUser extends \Beagle\Shared\Bus\QueryHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @param GetUserQuery $query
     *
     * @return GetUserResponse
     * @throws UserNotFound
     * @throws InvalidValueObject
     */
    protected function handle(Query $query):QueryResponse
    {
        $userId = UserId::fromString($query->userId());

        $user = $this->userRepository->find($userId);

        return new GetUserResponse($user);
    }
}
