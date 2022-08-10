<?php

declare(strict_types=1);

namespace App\Service\NotificationSender;

use App\Factory\MailerFactory;
use App\Form\MailConfigType;
use App\Model\NotificationSenderConfig\MailConfig;
use App\Model\NotificationSenderConfigInterface;
use InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

final class MailNotificationSender implements NotificationSenderInterface
{
    public function __construct(private readonly MailerFactory $mailerFactory)
    {
    }

    public static function getId(): string
    {
        return 'mail';
    }

    public static function getName(): string
    {
        return 'Mail';
    }

    public static function getConfigType(): string
    {
        return MailConfigType::class;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(NotificationSenderConfigInterface $config, string $message): void
    {
        if (!$config instanceof MailConfig) {
            throw new InvalidArgumentException(
                sprintf('Unexpected config type. Expected %s, %s given', MailConfig::class, $config::class)
            );
        }

        $mailer = $this->mailerFactory->create($config);

        $email = (new Email())
            ->from($config->getFrom())
            ->to($config->getTo())
            ->subject($config->getSubject())
            ->text($message);

        $mailer->send($email);
    }
}