<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\NewVideoEvent;
use App\Exception\NoConfigException;
use App\Service\ConsoleLogger;
use App\Service\ShinobiNewVideoFetcher;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsCommand('app:worker')]
final class WorkerCommand extends Command
{
    public function __construct(
        private readonly ShinobiNewVideoFetcher $newVideoFetcher,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ConsoleLogger $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug('app:worker started');

        sleep(1);

        try {
            foreach ($this->newVideoFetcher->fetch() as $video) {
                try {
                    $this->eventDispatcher->dispatch(new NewVideoEvent($video));
                } catch (Throwable $exception) {
                    $this->logger->critical($exception->getMessage());
                }
            }
        } catch (ClientExceptionInterface $exception) {
            $this->logger->error(sprintf('Shinobi connection error: %s', $exception->getMessage()));
        } catch (NoConfigException) {
            $this->logger->warning('App config not found. Please go to GUI and setup your config.');
        } catch (Throwable $exception) {
            $this->logger->critical($exception->getMessage());
        }

        $this->logger->debug('app:worker ended');

        return 0;
    }
}