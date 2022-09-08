<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidEmail extends \Exception
{
    private const INVALID_FORMAT = "The email %s has an invalid format";

    public static function byFormat(string $email): self
    {
        return new self(\sprintf(self::INVALID_FORMAT, $email));
    }
}
