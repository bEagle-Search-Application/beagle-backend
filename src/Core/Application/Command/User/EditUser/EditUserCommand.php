<?php declare(strict_types = 1);

namespace Beagle\Core\Application\Command\User\EditUser;

use Beagle\Shared\Bus\Command;

final class EditUserCommand implements Command
{
    public function __construct(
        private string $authorId,
        private string $userId,
        private string $email,
        private string $name,
        private string $surname,
        private string $phonePrefix,
        private string $phone,
        private string $location,
        private string $bio,
        private bool $showReviews,
    ) {
    }

    public function authorId():string
    {
        return $this->authorId;
    }

    public function userId():string
    {
        return $this->userId;
    }

    public function email():string
    {
        return $this->email;
    }

    public function name():string
    {
        return $this->name;
    }

    public function surname():string
    {
        return $this->surname;
    }

    public function phonePrefix():string
    {
        return $this->phonePrefix;
    }

    public function phone():string
    {
        return $this->phone;
    }

    public function location():string
    {
        return $this->location;
    }

    public function bio():string
    {
        return $this->bio;
    }

    public function showReviews():bool
    {
        return $this->showReviews;
    }
}
