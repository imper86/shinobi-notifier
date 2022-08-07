<?php

declare(strict_types=1);

namespace App\Service\NotificationSender;

use App\Model\NotificationSenderConfigInterface;

interface NotificationSenderInterface
{
    public static function getId(): string;

    public static function getName(): string;

    public static function getConfigType(): string;

    public function send(NotificationSenderConfigInterface $config, string $message): void;
}