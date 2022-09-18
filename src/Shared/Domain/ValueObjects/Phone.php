<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidPhone;

final class Phone
{
    /** @throws InvalidPhone */
    private function __construct(private string $value)
    {
        if (!\filter_var($this->value, FILTER_SANITIZE_NUMBER_INT)) {
            throw InvalidPhone::byFormat($this->value);
        }
    }

    /** @throws InvalidPhone */
    public static function fromString(string $phone):self
    {
        return new self($phone);
    }

    public function value():string
    {
        return $this->value;
    }

    public function equals(Phone $phone):bool
    {
        return $this->value === $phone->value;
    }
}
