<?php declare(strict_types = 1);

namespace Tests\MotherObjects;

use Beagle\Shared\Domain\ValueObjects\DateTime;

final class DateTimeMotherObject
{
    public static function now():DateTime
    {
        return DateTime::now();
    }

    public static function tomorrow():DateTime
    {
        return DateTime::tomorrow();
    }

    public static function yesterday():DateTime
    {
        return DateTime::yesterday();
    }

    public static function inFuture(int $minutes):DateTime
    {
        return DateTime::now()->addMinutes($minutes);
    }
}
