<?php

declare(strict_types=1);

namespace App\Model;

final class ShinobiConfig
{
    public function __construct(
        public string $schema,
        public string $host,
        public int $port,
        public string $apiKey,
        public string $groupKey,
    ) {
    }
}