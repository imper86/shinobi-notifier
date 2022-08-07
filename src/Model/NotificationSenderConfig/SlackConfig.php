<?php

declare(strict_types=1);

namespace App\Model\NotificationSenderConfig;

use App\Model\NotificationSenderConfigInterface;
use App\Service\NotificationSender\SlackNotificationSender;

final class SlackConfig implements NotificationSenderConfigInterface
{
    public function __construct(private bool $enabled, private string $apiKey)
    {
    }

    public static function getServiceId(): string
    {
        return SlackNotificationSender::getId();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}