<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Faker\Factory;

final class IntegerMotherObject
{
    public static function create(): int
    {
        return Factory::create()->randomNumber();
    }

    public static function createRating():int
    {
        return Factory::create()->numberBetween(0, 5);
    }

    public static function createWithDigits(int $digit):int
    {
        return Factory::create()->randomNumber($digit);
    }
}
