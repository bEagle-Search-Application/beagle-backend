<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;

final class User
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserPassword $password,
        private string $name,
        private string $surname,
        private ?string $bio,
        private string $location,
        private ?string $phone,
        private ?string $picture,
        private bool $showReviews,
        private int $rating
    )
    {
    }

    public function id():UserId
    {
        return $this->id;
    }

    public function email():UserEmail
    {
        return $this->email;
    }

    public function password():UserPassword
    {
        return $this->password;
    }

    public function name():string
    {
        return $this->name;
    }

    public function surname():string
    {
        return $this->surname;
    }

    public function bio():?string
    {
        return $this->bio;
    }

    public function location():string
    {
        return $this->location;
    }

    public function phone():?string
    {
        return $this->phone;
    }

    public function picture():?string
    {
        return $this->picture;
    }

    public function showReviews():bool
    {
        return $this->showReviews;
    }

    public function rating():int
    {
        return $this->rating;
    }
}
