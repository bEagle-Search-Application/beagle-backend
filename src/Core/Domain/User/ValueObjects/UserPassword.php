<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

use Beagle\Core\Domain\User\Errors\InvalidPassword;

final class UserPassword
{
    /** @throws InvalidPassword */
    private function __construct(private string $value)
    {
        $this->checkIfPasswordEncryptionIsInvalid();
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

    /** @throws InvalidPassword */
    private function checkIfPasswordEncryptionIsInvalid():void
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $this->value)) {
            throw InvalidPassword::byEncryption();
        }
    }
}
