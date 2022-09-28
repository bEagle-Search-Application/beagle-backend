<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User;

use Beagle\Core\Domain\User\Errors\InvalidUserVerification;
use Beagle\Shared\Domain\ValueObjects\DateTime;
use Beagle\Shared\Domain\ValueObjects\Email;
use Beagle\Shared\Domain\ValueObjects\Guid;
use Beagle\Shared\Domain\ValueObjects\Token;

final class UserVerification
{
    public function __construct(
        private Guid $id,
        private Email $email,
        private Token $token,
        private DateTime $expiredAt
    ) {
    }

    public static function create(Email $email):self
    {
        return new self(
            Guid::generate(),
            $email,
            Token::generateRandom64String(),
            DateTime::now()->addMinutes(30)
        );
    }

    public function id():Guid
    {
        return $this->id;
    }

    public function email():Email
    {
        return $this->email;
    }

    public function token():Token
    {
        return $this->token;
    }

    public function expiredAt():DateTime
    {
        return $this->expiredAt;
    }

    /** @throws InvalidUserVerification */
    public function checkIfExpired():void
    {
        if (DateTime::now()->greaterThan($this->expiredAt)) {
            throw InvalidUserVerification::byExpiredAt($this->expiredAt);
        }
    }

    /** @throws InvalidUserVerification */
    public function ensureIfEmailToVerifyIsCorrect(Email $email):void
    {
        if (!$this->email->equals($email)) {
            throw InvalidUserVerification::byEmail($email);
        }
    }
}
