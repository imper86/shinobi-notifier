<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class AppConfig
{
    public function __construct(
        public ShinobiConfig $shinobi,
        public array $activeMonitorIds = [],
        public ?DateTimeImmutable $lastVideoAppearedAt = null,
        private array $notificationSenders = [],
    ) {
    }

    /**
     * @return NotificationSenderConfigInterface[]
     */
    public function getNotificationSenders(): array
    {
        return $this->notificationSenders;
    }

    public function getNotificationSender(int $key): ?NotificationSenderConfigInterface
    {
        return $this->notificationSenders[$key] ?? null;
    }

    public function addNotificationSender(NotificationSenderConfigInterface $senderConfig): void
    {
        $this->notificationSenders[] = $senderConfig;
    }

    public function removeNotificationSender(int $key): void
    {
        if (array_key_exists($key, $this->notificationSenders)) {
            array_splice($this->notificationSenders, $key, 1);
        }
    }

    public function changeNotificationSender(int $key, NotificationSenderConfigInterface $senderConfig): void
    {
        if (array_key_exists($key, $this->notificationSenders)) {
            $this->notificationSenders[$key] = $senderConfig;
        }
    }
}