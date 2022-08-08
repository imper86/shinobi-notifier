<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\AppConfigRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:config:create-empty')]
final class ConfigCreateEmptyCommand extends Command
{
    public function __construct(private AppConfigRepository $configRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force creation when config already exists',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');

        if (!$force && $this->configRepository->exists()) {
            $io->error('Config already exists. Use -f option to force overwrite');

            return 1;
        }

        $config = $this->configRepository->createEmpty();
        $this->configRepository->save($config);

        $io->success('Created empty config file');

        return 0;
    }
}