<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Shared\Domain\Errors\InvalidEmail;
use Beagle\Shared\Domain\ValueObjects\Email;
use Faker\Factory;

final class EmailMotherObject
{
    /** @throws InvalidEmail */
    public static function create(?string $email = null):Email
    {
        return Email::fromString($email ?? Factory::create()->email);
    }
}
