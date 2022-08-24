<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Core\Domain\User\User;

final class LoginResponse
{
    public function __construct(
        private User $user,
        private array $auth
    )
    {
    }

    public function toArray():array
    {
        return [
            "user" => [
                "id" => $this->user->id()->value(),
                "email" => $this->user->email()->value(),
                "name" => $this->user->name(),
                "surname" => $this->user->surname(),
                "bio" => $this->user->bio(),
                "location" => $this->user->location(),
                "phone" => $this->user->phone(),
                "picture" => $this->user->picture(),
                "show_reviews" => $this->user->showReviews(),
                "rating" => $this->user->rating(),
            ],
            "auth" => $this->auth,
        ];
    }
}
