<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\NotificationSenderConfig\MailConfig;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MailerFactory
{
    public function __construct(
        private readonly Transport $transport,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function create(MailConfig $config): MailerInterface
    {
        $transport = $this->transport::fromDsn(
            $config->getDsn(),
            $this->eventDispatcher,
            $this->httpClient,
        );

        return new Mailer($transport, dispatcher: $this->eventDispatcher);
    }
}