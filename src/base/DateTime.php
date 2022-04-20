<?php

namespace app\base;

use DateInterval;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

use function array_combine;
use function array_keys;
use function date_diff;
use function date_format;
use function date_offset_get;
use function explode;
use function get_object_vars;
use function in_array;
use function is_numeric;
use function is_string;
use function strtotime;

/**
 * Custom immutable implementation of `DateTimeInterface`.
 */
class DateTime implements DateTimeInterface
{
    /**
     * SQL datetime/timestamp format.
     */
    public const SQL = 'Y-m-d H:i:s';
    /**
     * A full textual representation of the days of the week.
     */
    public const DAYS_OF_WEEK = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    ];

    /**
     * @var int The current time stamp
     */
    protected int $timestamp;
    /**
     * @var string The output format by default
     */
    protected string $format;
    /**
     * @var int|false The time zone offset
     */
    protected $offset;
    /**
     * @var DateTimeZone The time zone instance
     */
    protected DateTimeZone $timeZone;
    /**
     * @var array|null The cached results
     */
    protected array $data;

    /**
     * Configurates new instance.
     *
     * @param string $dateTime The date/time string or UNIX timestamp
     * @param string $format The output format by default
     * @param string|DateTimeZone $timeZone The time zone
     * @throws InvalidArgumentException If invalid time zone
     */
    public function __construct(string $dateTime = 'now', string $format = self::SQL, $timeZone = 'UTC')
    {
        if (empty($dateTime)) {
            $dateTime = 'now';
        } elseif (is_numeric($dateTime)) {
            // normalize UNIX timestamp
            $dateTime = '@' . $dateTime;
        }
        $this->timestamp = strtotime($dateTime);

        $this->format = $format;

        if (is_string($timeZone)) {
            $timeZone = new DateTimeZone($timeZone);
        }
        if (!$timeZone instanceof DateTimeZone) {
            throw new InvalidArgumentException('Invalid time zone');
        }
        $this->timeZone = $timeZone;
    }

    /**l
     * Returns the difference between two `DateTimeInterface` objects.
     *
     * @param DateTimeInterface $targetObject The date to compare to
     * @param bool $absolute Should the interval be forced to be positive?
     * @return DateInterval
     */
    public function diff(DateTimeInterface $targetObject, $absolute = false): DateInterval
    {
        return date_diff($this, $targetObject, (bool) $absolute);
    }

    /**
     * Returns date formatted according to given format.
     *
     * @param string|null $format Format accepted by `date()`
     * @return string
     */
    public function format($format = null): string
    {
        return date_format($this, $format ?: $this->format);
    }

    /**
     * Checks if current date/time in given range.
     *
     * @param DateTimeInterface $begin The start date of the range
     * @param DateTimeInterface $end The end date of the range
     * @param bool $strict If TRUE, excludes range dates, else, they are includes (by default)
     * @return bool
     */
    public function inRange(DateTimeInterface $begin, DateTimeInterface $end, bool $strict = false): bool
    {
        if ($strict) {
            return $this->getTimestamp() > $begin->getTimestamp() && $this->getTimestamp() < $end->getTimestamp();
        }

        return $this->getTimestamp() >= $begin->getTimestamp() && $this->getTimestamp() <= $end->getTimestamp();
    }

    /**
     * Returns new instance of current date/time +/- given modifier.
     *
     * @param string $modifier A date/time string, valid format of `strtotime()`: '+2 months', '1 year' or '-5 days'.
     * @return static
     */
    public function modify(string $modifier): self
    {
        return new static(strtotime($modifier, $this->getTimestamp()), $this->format, $this->getTimezone());
    }

    /**
     * Returns the time zone offset.
     *
     * @return int|false
     */
    public function getOffset()
    {
        if (!isset($this->offset)) {
            $this->offset = date_offset_get($this);
        }

        return $this->offset;
    }

    /**
     * Gets the UNIX time stamp.
     *
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Return time zone relative to given time.
     *
     * @return DateTimeZone|false
     */
    public function getTimezone()
    {
        return $this->timeZone ?? false;
    }

    /**
     * Returns the year number.
     *
     * @return int
     */
    public function getYear(): int
    {
        return (int) $this->getData()['year'];
    }

    /**
     * Returns the month number: 1 - january, 12 - december.
     *
     * @return int
     */
    public function getMonth(): int
    {
        return (int) $this->getData()['month'];
    }

    /**
     * Returns the number of the day in the month.
     *
     * @return int
     */
    public function getDay(): int
    {
        return (int) $this->getData()['day'];
    }

    /**
     * Returns the numeric representation of the day of the week: 0 (Sunday) through 6 (Saturday).
     *
     * @return int
     */
    public function getDayOfWeek(): int
    {
        return (int) $this->getData()['dayOfWeek'];
    }

    /**
     * Returns the full textual representation of the day of the week: Sunday through Saturday.
     *
     * @return string
     */
    public function getDayName(): string
    {
        return static::DAYS_OF_WEEK[$this->getDayOfWeek()];
    }

    /**
     * Checking "today is weekend"?
     *
     * @return bool
     */
    public function isWeekend(): bool
    {
        return in_array((int) $this->getData()['dayOfWeek'], [0, 6], true);
    }

    /**
     * Returns the number of the day in the year.
     *
     * @return int
     */
    public function getDayOfYear(): int
    {
        return (int) $this->getData()['dayOfYear'];
    }

    /**
     * Returns the hour number in 24 hours format.
     *
     * @return int
     */
    public function getHour(): int
    {
        return (int) $this->getData()['hour'];
    }

    /**
     * Returns parsed date.
     *
     * @return array
     */
    protected function getData(): array
    {
        if (!isset($this->data)) {
            $this->data = array_combine(
                ['year', 'month', 'day', 'dayOfWeek', 'dayOfYear', 'hour'],
                explode(',', $this->format('Y,n,j,w,z,G'))
            );
        }

        return $this->data;
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return array_keys($this->__debugInfo());
    }

    /**
     * Initializes a unserialized object.
     *
     * @return void
     */
    public function __wakeup()
    {
        if (isset($this->timeZone)) {
            $this->timeZone = new DateTimeZone($this->getTimezone());
        }
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
        return get_object_vars($this);
    }

    /**
     * Clones time zone instance.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->getTimezone()) {
            $this->timeZone = clone $this->getTimezone();
        }
    }
}
