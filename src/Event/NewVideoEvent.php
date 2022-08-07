<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\ShinobiApi\Video;
use Symfony\Contracts\EventDispatcher\Event;

final class NewVideoEvent extends Event
{
    public function __construct(public readonly Video $video)
    {
    }
}