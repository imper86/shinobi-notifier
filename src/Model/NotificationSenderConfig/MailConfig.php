<?php

declare(strict_types=1);

namespace App\Model\NotificationSenderConfig;

use App\Model\NotificationSenderConfigInterface;

final class MailConfig implements NotificationSenderConfigInterface
{
    public function __construct(
        private bool $enabled,
        private string $dsn,
        private string $from,
        private string $to,
        private string $subject,
    ) {
    }

    public static function getServiceId(): string
    {
        return 'mail';
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function setDsn(string $dsn): void
    {
        $this->dsn = $dsn;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}