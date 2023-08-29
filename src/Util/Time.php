<?php

namespace App\Util;

use App\Util\Comparable\Comparable;
use App\Util\Comparable\ComparableInterface;

class Time implements ComparableInterface
{
    use Comparable;

    public function __construct(
        protected int $hour,
        protected int $minute,
        protected int $second,
    ) {}

    public function __toString(): string
    {
        return $this->format('H:i:s');
    }

    public function format(string $format): string
    {
        if ($format === 'H:i:s') {
            return "{$this->hour}:{$this->minute}:{$this->second}";
        }

        $dateTime = \DateTime::createFromFormat(
            'H:i:s',
            sprintf('%02d:%02d:%02d', $this->hour, $this->minute, $this->second)
        );

        return $dateTime->format($format);
    }

    public static function now(): self
    {
        return self::createFromDateTime(new \DateTime());
    }

    public static function createFromDateTime(\DateTimeInterface $dateTime): self
    {
        $timeString = $dateTime->format('H:i:s');
        $timeValues = explode(':', $timeString);
        array_map(fn (string $value) => (int) $value, $timeValues);

        return new self(...$timeValues);
    }

    public static function createFromTimeString(string $timeString): self
    {
        if (!preg_match('/^\d+(:\d+)+$/', $timeString)) {
            throw new \InvalidArgumentException('Invalid time string.');
        }

        $time = explode(':', $timeString);

        $hour = (int) $time[0];
        $minute = (int) $time[1];
        $second = array_key_exists(2, $time) ? (int) $time[2] : 0;

        return new self($hour, $minute, $second);
    }

    public function getValue(): int
    {
        return $this->hour * 3600 + $this->minute * 60 + $this->second;
    }
}
