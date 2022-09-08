<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidEmail;

final class UserEmail
{
    /** @throws InvalidEmail */
    private function __construct(private string $value)
    {
        if (!\filter_var($this->value, FILTER_VALIDATE_EMAIL))
        {
            throw InvalidEmail::byFormat($this->value);
        }
    }

    public function value():string
    {
        return $this->value;
    }

    /** @throws InvalidEmail */
    public static function fromString(string $email):self
    {
        return new self($email);
    }

    public function equals(UserEmail $userEmail):bool
    {
        return $this->value === $userEmail->value();
    }
}
