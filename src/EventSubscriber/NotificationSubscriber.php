<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\NewVideoEvent;
use App\Service\AppConfigRepository;
use App\Service\NotificationSender\NotificationSenderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class NotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AppConfigRepository $configRepository,
        private readonly ServiceLocator $notificationSenderLocator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewVideoEvent::class => ['onNewVideo', 0],
        ];
    }

    public function onNewVideo(NewVideoEvent $event): void
    {
        $config = $this->configRepository->get();

        if (null === $config) {
            return;
        }

        if (!in_array($event->video->mid, $config->activeMonitorIds, true)) {
            return;
        }

        foreach ($config->getNotificationSenders() as $senderConfig) {
            if ($senderConfig->isEnabled()) {
                /** @var NotificationSenderInterface $sender */
                $sender = $this->notificationSenderLocator->get($senderConfig::getServiceId());
                $sender->send(
                    $senderConfig,
                    sprintf(
                        '[%s] new video found at %s - file %s',
                        $event->video->mid,
                        $event->video->time->format('c'),
                        $event->video->filename,
                    ),
                );
            }
        }
    }
}