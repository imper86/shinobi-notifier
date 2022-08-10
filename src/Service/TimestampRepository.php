<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class TimestampRepository
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function save(string $key, DateTimeImmutable $time): void
    {
        file_put_contents($this->getFilePath($key), $time->format('c'));
    }

    /**
     * @throws Exception
     */
    public function load(string $key): ?DateTimeImmutable
    {
        $path = $this->getFilePath($key);

        return file_exists($path) ? new DateTimeImmutable(file_get_contents($path)) : null;
    }

    private function getFilePath(string $key): string
    {
        return sprintf('%s/var/config/timestamp_%s.json', $this->parameterBag->get('kernel.project_dir'), $key);
    }
}