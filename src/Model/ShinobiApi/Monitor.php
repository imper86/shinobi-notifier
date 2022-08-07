<?php

declare(strict_types=1);

namespace App\Model\ShinobiApi;

final class Monitor
{
    public function __construct(
        public readonly string $mid,
        public readonly string $ke,
        public readonly string $name,
    ) {
    }
}