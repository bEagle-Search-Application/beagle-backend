<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

final class UserToken
{
    private const TYPE = "Bearer";

    private function __construct(private ?string $value)
    {
    }

    public function value():?string
    {
        return $this->value;
    }

    public static function fromString(string $token):self
    {
        return new self($token);
    }

    public function equals(UserToken $userToken):bool
    {
        return $this->value === $userToken->value();
    }

    public function clear():self
    {
        return new self(\trim(\str_replace(self::TYPE, "", $this->value)));
    }

    public function clearValue():string
    {
        return $this->clear()->value;
    }

    public function type():string
    {
        return self::TYPE;
    }
}
