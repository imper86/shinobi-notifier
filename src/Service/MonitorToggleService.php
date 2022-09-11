<?php

namespace App\Service;

use App\Event\MonitorToggleEvent;
use App\Exception\EmptyConfigException;
use App\Exception\NoConfigException;
use App\Exception\ShinobiApiException;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MonitorToggleService
{
    public function __construct(
        private readonly AppConfigRepository $configRepository,
        private readonly ShinobiApi $shinobiApi,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    public function toggle(string $monitorId): void
    {
        if ($this->isTurnedOn($monitorId)) {
            $this->turnOff($monitorId);
        } else {
            $this->turnOn($monitorId);
        }
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    public function turnOn(string $monitorId): void
    {
        if ($this->isTurnedOn($monitorId)) {
            return;
        }

        $config = $this->configRepository->get();
        $config->activeMonitorIds[] = $monitorId;
        $this->configRepository->save($config);

        $this->eventDispatcher->dispatch(new MonitorToggleEvent($monitorId, true));
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    public function turnOff(string $monitorId): void
    {
        if (!$this->isTurnedOn($monitorId)) {
            return;
        }

        $config = $this->configRepository->get();

        array_splice(
            $config->activeMonitorIds,
            array_search($monitorId, $config->activeMonitorIds),
            1
        );

        $this->configRepository->save($config);

        $this->eventDispatcher->dispatch(new MonitorToggleEvent($monitorId, false));
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     * @throws ShinobiApiException
     * @throws ClientExceptionInterface
     */
    public function turnOnAll(): void
    {
        foreach ($this->shinobiApi->getMonitors() as $monitor) {
            $this->turnOn($monitor->mid);
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws EmptyConfigException
     * @throws NoConfigException
     * @throws ShinobiApiException
     */
    public function turnOffAll(): void
    {
        foreach ($this->shinobiApi->getMonitors() as $monitor) {
            $this->turnOff($monitor->mid);
        }
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    private function isTurnedOn(string $monitorId): bool
    {
        $config = $this->configRepository->get();

        return in_array($monitorId, $config->activeMonitorIds, true);
    }
}