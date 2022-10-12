<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

final class UserPassword
{
    private function __construct(private string $value)
    {
    }

    public function value():string
    {
        return $this->value;
    }

    public static function fromString(string $password):self
    {
        return new self($password);
    }

    public function equals(UserPassword $userPassword):bool
    {
        return $this->value === $userPassword->value();
    }
}
