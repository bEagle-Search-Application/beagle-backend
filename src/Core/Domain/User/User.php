<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Event\UserCreated;
use Beagle\Core\Domain\User\Event\UserEmailEdited;
use Beagle\Core\Domain\User\ValueObjects\UserEmail;
use Beagle\Core\Domain\User\ValueObjects\UserId;
use Beagle\Core\Domain\User\ValueObjects\UserPassword;
use Beagle\Core\Domain\User\ValueObjects\UserPhone;
use Beagle\Shared\Domain\Entity;

final class User extends Entity
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserPassword $password,
        private string $name,
        private string $surname,
        private ?string $bio,
        private ?string $location,
        private UserPhone $phone,
        private ?string $picture,
        private bool $showReviews,
        private int $rating,
        private bool $isVerified,
    ) {
    }

    public static function createWithBasicInformation(
        UserId $id,
        UserEmail $email,
        UserPassword $password,
        string $name,
        string $surname,
        UserPhone $phone
    ):self {
        $user = new self(
            $id,
            $email,
            $password,
            $name,
            $surname,
            null,
            null,
            $phone,
            null,
            true,
            0,
            false,
        );

        $user->recordThat(
            new UserCreated($user->email())
        );

        return $user;
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

    public function location():?string
    {
        return $this->location;
    }

    public function phone():UserPhone
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

    public function isVerified():bool
    {
        return $this->isVerified;
    }

    public function verify():void
    {
        $this->isVerified = true;
    }

    public function updateEmailBeforeVerify(UserEmail $userEmail):void
    {
        $oldEmail = $this->email;

        $this->email = $userEmail;
        $this->recordThat(
            new UserEmailEdited(
                $this->id,
                $oldEmail,
                $userEmail
            )
        );
    }

    public function updateEmailAfterVerify(UserEmail $userEmail):void
    {
        $this->email = $userEmail;
    }

    public function updateName(string $userName):void
    {
        $this->name = $userName;
    }

    public function updateSurname(string $userSurname):void
    {
        $this->surname = $userSurname;
    }

    public function updatePhone(UserPhone $userPhone):void
    {
        $this->phone = $userPhone;
    }

    public function updateLocation(string $userLocation):void
    {
        $this->location = $userLocation;
    }

    public function updateBio(string $userBio):void
    {
        $this->bio = $userBio;
    }

    public function disableReviews():void
    {
        $this->showReviews = false;
    }

    public function activeReviews():void
    {
        $this->showReviews = true;
    }
}
