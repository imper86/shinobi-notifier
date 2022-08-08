<?php

declare(strict_types=1);

namespace App\Twig;

use App\Exception\EmptyConfigException;
use App\Exception\NoConfigException;
use App\Service\AppConfigRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IsMonitorActiveExtension extends AbstractExtension
{
    public function __construct(private readonly AppConfigRepository $configRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isMonitorActive', [$this, 'isMonitorActive']),
        ];
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    public function isMonitorActive(string $monitorId): bool
    {
        return in_array($monitorId, $this->configRepository->get()->activeMonitorIds, true);
    }
}