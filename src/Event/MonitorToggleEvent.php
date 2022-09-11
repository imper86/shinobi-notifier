<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class MonitorToggleEvent extends Event
{
    public function __construct(public readonly string $monitorId, public readonly bool $status)
    {
    }
}