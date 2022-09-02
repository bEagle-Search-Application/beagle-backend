<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Faker\Factory;

final class BooleanMotherObject
{
    public static function create():bool
    {
        return Factory::create()->boolean;
    }
}
