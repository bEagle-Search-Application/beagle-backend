<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Beagle\Shared\Domain\Errors\InvalidDateTimeString;
use Carbon\CarbonImmutable;

final class DateTime extends CarbonImmutable
{
    public const  DEFAULT_HOUR_FORMAT                               = 'H:i:s';
    public const  DEFAULT_DATE_FORMAT                               = 'Y-m-d';
    public const  DEFAULT_DATE_TIME_FORMAT                          = 'Y-m-d H:i:s';
    public const  DATE_HOUR_AND_MINUTE_WITH_TIME_ZONE_FORMAT        = 'Y-m-d\TH:iP';
    private const DATE_HOUR_MINUTE_AND_SECOND_WITH_TIME_ZONE_FORMAT = 'Y-m-d\TH:i:sP';
    private const DATE_HOUR_MINUTE_AND_SECOND_AND_MILLISECOND       = 'Y-m-d\TH:i:s.v\Z';
    private const ACCEPTED_FORMATS                                  = [
        self::DEFAULT_DATE_FORMAT,
        self::DEFAULT_DATE_TIME_FORMAT,
        self::DATE_HOUR_AND_MINUTE_WITH_TIME_ZONE_FORMAT,
        self::DATE_HOUR_MINUTE_AND_SECOND_WITH_TIME_ZONE_FORMAT,
        self::DATE_HOUR_MINUTE_AND_SECOND_AND_MILLISECOND,
    ];

    /** @throws InvalidDateTimeString */
    public static function createFromString(string $dateTime): DateTime
    {
        foreach (self::ACCEPTED_FORMATS as $acceptedFormat) {
            try {
                return DateTime::createFromFormat($acceptedFormat, $dateTime);
            } catch (\InvalidArgumentException $exception) {
                continue;
            }
        }

        throw InvalidDateTimeString::invalidValue($dateTime);
    }
}
