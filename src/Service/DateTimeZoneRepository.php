<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeZone;

final class DateTimeZoneRepository
{
    private ?DateTimeZone $utc = null;

    private ?DateTimeZone $local = null;

    public function __construct(private readonly AppConfigRepository $configRepository)
    {
    }

    public function getUtc(): DateTimeZone
    {
        return $this->utc ??= new DateTimeZone('UTC');
    }

    public function getLocal(): DateTimeZone
    {
        $config = $this->configRepository->get();

        return $this->local ??= new DateTimeZone($config->timezone);
    }
}