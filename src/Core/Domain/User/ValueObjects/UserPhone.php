<?php declare(strict_types = 1);

namespace Beagle\Core\Domain\User\ValueObjects;

use Beagle\Shared\Domain\ValueObjects\Phone;
use Beagle\Shared\Domain\ValueObjects\PhonePrefix;

final class UserPhone
{
    private function __construct(
        private PhonePrefix $phonePrefix,
        private Phone $phone
    ) {
    }

    public static function create(PhonePrefix $prefixPhone, Phone $phone): self
    {
        return new self($prefixPhone, $phone);
    }

    public function phone():Phone
    {
        return $this->phone;
    }

    public function phoneAsString():string
    {
        return $this->phone->value();
    }

    public function phonePrefix():PhonePrefix
    {
        return $this->phonePrefix;
    }

    public function phonePrefixAsString():string
    {
        return $this->phonePrefix->value();
    }

    public function equals(UserPhone $userPhone): bool
    {
        return $this->phone->equals($userPhone->phone()) && $this->phonePrefix->equals($userPhone->phonePrefix());
    }
}
