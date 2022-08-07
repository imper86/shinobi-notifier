<?php

declare(strict_types=1);

namespace App\Model\ShinobiApi;

use DateTimeImmutable;

final class Video
{
    public function __construct(
        public readonly string $mid,
        public readonly string $ke,
        public readonly string $ext,
        public readonly DateTimeImmutable $time,
        public readonly DateTimeImmutable $end,
        public readonly int $status,
        public readonly string $filename,
        public readonly string $actionUrl,
        public readonly string $href,
    ) {
    }
}