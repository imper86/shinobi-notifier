<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

final class DateTimeConverter
{
    public function __construct(private readonly DateTimeZoneRepository $timeZoneRepository)
    {
    }

    public function toLocal(DateTimeInterface $time): DateTime
    {
        $local = DateTime::createFromInterface($time);
        $local->setTimezone($this->timeZoneRepository->getLocal());

        return $local;
    }

    public function toLocalImmutable(DateTimeInterface $time): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable($this->toLocal($time));
    }

    public function toUtc(DateTimeInterface $time): DateTime
    {
        $utc = DateTime::createFromInterface($time);
        $utc->setTimezone($this->timeZoneRepository->getUtc());

        return $utc;
    }

    public function toUtcImmutable(DateTimeInterface $time): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable($this->toUtc($time));
    }
}