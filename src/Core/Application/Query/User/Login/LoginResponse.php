<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Query\User\Login;

use Beagle\Core\Domain\PersonalToken\PersonalAccessToken;
use Beagle\Core\Domain\PersonalToken\PersonalRefreshToken;
use Beagle\Core\Domain\User\User;
use Beagle\Shared\Bus\QueryResponse;

final class LoginResponse implements QueryResponse
{
    public function __construct(
        private User $user,
        private PersonalAccessToken $accessToken,
        private PersonalRefreshToken $refreshToken
    ) {
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
                "phone_prefix" => $this->user->phone()->phonePrefixAsString(),
                "phone" => $this->user->phone()->phoneAsString(),
                "picture" => $this->user->picture(),
                "show_reviews" => $this->user->showReviews(),
                "rating" => $this->user->rating(),
                "is_verified" => $this->user->isVerified()
            ],
            "auth" => [
                "access_token" => $this->accessToken->token()->value(),
                "refresh_token" => $this->refreshToken->token()->value()
            ],
        ];
    }
}
