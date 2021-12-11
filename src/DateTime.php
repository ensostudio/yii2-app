<?php

namespace app;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Extended date/time object.
 */
class DateTime extends DateTimeImmutable
{
    /**
     * @var string
     */
    public const SQL_DATE = 'Y-m-d';
    /**
     * @var string
     */
    public const SQL_TIME = 'H:i:s';
    /**
     * @var string
     */
    public const SQL = self::SQL_DATE . ' ' . self::SQL_TIME;

    /**
     * @var string The date/time format by default
     */
    public $format;

    /**
     * Configurates new instance.
     *
     * @param string|int|null $datetime The date/time string or timestamp
     * @param string $format The date/time format by default
     * @param DateTimeZone|null $timezone The time zone
     * @throws \Exception If invalid $datetime
     */
    public function __construct($datetime = 'now', string $format = self::SQL, DateTimeZone $timezone = null)
    {
        if (empty($datetime)) {
            $datetime = 'now';
        }
        // converts to UNIX timestamp
        if (is_numeric($datetime)) {
            $datetime = '@' . $datetime;
        }
        parent::__construct($datetime, $timezone);
        $this->format = $format;
    }

    /**
     * Returns date/time formatted according to given format.
     *
     * @param string|null $format The date/time format, ommit to use `$this->format`
     * @return string
     */
    public function format($format = null): string
    {
        return parent::format($format ?: $this->format);
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return array_keys((array) $this);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format($this->format);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return (array) $this;
    }

    /**
     * Checks today is weekend?
     *
     * @return bool
     */
    public function isWeekend(): bool
    {
        return in_array($this->format('w'), ['6', '0'], true);
    }
}
