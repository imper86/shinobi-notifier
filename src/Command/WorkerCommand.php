<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\NewVideoEvent;
use App\Exception\NoConfigException;
use App\Service\ShinobiNewVideoFetcher;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand('app:worker')]
final class WorkerCommand extends Command
{
    public function __construct(
        private readonly ShinobiNewVideoFetcher $newVideoFetcher,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        sleep(1);

        $io = new SymfonyStyle($input, $output);

        try {
            foreach ($this->newVideoFetcher->fetch() as $video) {
                $this->eventDispatcher->dispatch(new NewVideoEvent($video));
            }
        } catch (ClientExceptionInterface $exception) {
            $io->error(sprintf('Shinobi connection error: %s', $exception->getMessage()));
        } catch (NoConfigException) {}

        return 0;
    }
}