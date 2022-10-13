<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidPhone;

class Phone
{
    /** @throws InvalidPhone */
    protected function __construct(private string $value)
    {
        $this->checkIfPhoneIsInvalid();
    }

    /** @throws InvalidPhone */
    public static function fromString(string $phone):static
    {
        return new static($phone);
    }

    public function value():string
    {
        return $this->value;
    }

    public function equals(Phone $phone):bool
    {
        return $this->value === $phone->value;
    }

    /** @throws InvalidPhone */
    private function checkIfPhoneIsInvalid():void
    {
        if (!\filter_var($this->value, FILTER_SANITIZE_NUMBER_INT)) {
            throw InvalidPhone::byFormat($this->value);
        }
    }
}
