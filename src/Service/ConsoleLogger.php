<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleLogger implements LoggerInterface
{
    use LoggerTrait;

    private OutputInterface $output;

    public function __construct(private readonly DateTimeZoneRepository $timeZoneRepository)
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * @throws Exception
     */
    public function log($level, $message, array $context = []): void
    {
        $now = new DateTimeImmutable('now', $this->timeZoneRepository->getUtc());

        $this->output->writeln(
            sprintf("%s\t%s\t%s", $now->format('Y-m-d\TH:i:s.v\Z'), strtoupper($level), $message)
        );
    }
}