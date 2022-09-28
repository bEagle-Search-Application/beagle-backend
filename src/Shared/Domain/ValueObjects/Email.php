<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidEmail;

class Email
{
    /** @throws InvalidEmail */
    protected function __construct(private string $value)
    {
        if (!\filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmail::byFormat($this->value);
        }
    }

    /** @throws InvalidEmail */
    public static function fromString(string $email):static
    {
        return new static($email);
    }

    public function value():string
    {
        return $this->value;
    }

    public function equals(Email $email):bool
    {
        return $this->value === $email->value();
    }
}
