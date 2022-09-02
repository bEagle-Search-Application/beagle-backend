<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidPassword;

final class UserPassword
{
    private const MIN_LENGTH = 8;

    /** @throws InvalidPassword */
    private function __construct(private string $value)
    {
        if (\strlen($this->value) < self::MIN_LENGTH)
        {
            throw InvalidPassword::byLength(self::MIN_LENGTH);
        }
    }

    public function value():string
    {
        return $this->value;
    }

    /** @throws InvalidPassword */
    public static function fromString(string $password):self
    {
        return new self($password);
    }

    public function equals(UserPassword $userPassword):bool
    {
        return $this->value === $userPassword->value();
    }
}
