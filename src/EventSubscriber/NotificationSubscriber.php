<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\MonitorToggleEvent;
use App\Event\NewVideoEvent;
use App\Exception\EmptyConfigException;
use App\Exception\NoConfigException;
use App\Service\AppConfigRepository;
use App\Service\ConsoleLogger;
use App\Service\DateTimeConverter;
use App\Service\NotificationSender\NotificationSenderInterface;
use App\Service\ShinobiApi;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class NotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AppConfigRepository $configRepository,
        private readonly ServiceLocator $notificationSenderLocator,
        private readonly ConsoleLogger $logger,
        private readonly DateTimeConverter $timeConverter,
        private readonly ShinobiApi $shinobiApi,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewVideoEvent::class => ['onNewVideo', 0],
            MonitorToggleEvent::class => ['onMonitorToggle', 0],
        ];
    }

    public function onMonitorToggle(MonitorToggleEvent $event): void
    {
        $config = $this->configRepository->get();

        foreach ($config->getNotificationSenders() as $senderConfig) {
            if ($senderConfig->isEnabled()) {
                /** @var NotificationSenderInterface $sender */
                $sender = $this->notificationSenderLocator->get($senderConfig::getServiceId());
                $sender->send(
                    $senderConfig,
                    sprintf('[%s] turned %s', $event->monitorId, $event->status ? 'on' : 'off'),
                );
            }
        }
    }

    /**
     * @throws Exception
     * @throws EmptyConfigException
     * @throws NoConfigException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onNewVideo(NewVideoEvent $event): void
    {
        $config = $this->configRepository->get();
        $video = $event->video;

        $this->logger->debug(sprintf('[NotificationSubscriber] found new video %s %s', $video->mid, $video->filename));

        if (!in_array($event->video->mid, $config->activeMonitorIds, true)) {
            $this->logger->debug(
                sprintf('[NotificationSubscriber] %s is not an active monitor, skipping', $video->mid)
            );

            return;
        }

        foreach ($config->getNotificationSenders() as $senderConfig) {
            if ($senderConfig->isEnabled()) {
                $this->logger->debug(
                    sprintf(
                        '[NotificationSubscriber] found active sender %s - sending notification',
                        $senderConfig::getServiceId(),
                    ),
                );

                /** @var NotificationSenderInterface $sender */
                $sender = $this->notificationSenderLocator->get($senderConfig::getServiceId());
                $sender->send(
                    $senderConfig,
                    sprintf(
                        '[%s] new video found at %s - %s%s',
                        $video->mid,
                        $this->timeConverter->toLocalImmutable($video->time)->format('Y-m-d H:i:s'),
                        $this->shinobiApi->getBasePath(),
                        $video->href,
                    ),
                );
            }
        }
    }
}