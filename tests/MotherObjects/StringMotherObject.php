<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Faker\Factory;

final class StringMotherObject
{
    public static function create():string
    {
        return Factory::create()->text;
    }

    public static function createNumber():string
    {
        return (string) IntegerMotherObject::create();
    }

    public static function createName():string
    {
        return Factory::create()->name;
    }

    public static function createSurname():string
    {
        return Factory::create()->lastName;
    }

    public static function createLocation():string
    {
        return Factory::create()->locale;
    }

    public static function createPath():string
    {
        return Factory::create()->filePath();
    }

    public static function createPhone():string
    {
        return Factory::create()->phoneNumber;
    }
}
