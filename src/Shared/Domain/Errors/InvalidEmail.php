<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\Errors;

final class InvalidEmail extends InvalidValueObject
{
    private const INVALID_FORMAT = "El email %s tiene un formato inválido";

    public static function byFormat(string $email): self
    {
        return new self(\sprintf(self::INVALID_FORMAT, $email));
    }
}
