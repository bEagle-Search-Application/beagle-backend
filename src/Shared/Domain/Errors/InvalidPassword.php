<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidPassword extends \Exception
{
    private const INVALID_LENGTH = "The password must have %s characters";

    public static function byLength(int $length): self
    {
        return new self(\sprintf(self::INVALID_LENGTH, $length));
    }
}
