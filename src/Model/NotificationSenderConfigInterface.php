<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\NotificationSenderConfig\SlackConfig;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[DiscriminatorMap(
    'type',
    [
        'slack' => SlackConfig::class,
    ]
)]
interface NotificationSenderConfigInterface
{
    public static function getServiceId(): string;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;
}