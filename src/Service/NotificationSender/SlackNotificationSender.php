<?php

declare(strict_types=1);

namespace App\Service\NotificationSender;

use App\Form\SlackConfigType;
use App\Model\NotificationSenderConfig\SlackConfig;
use App\Model\NotificationSenderConfigInterface;
use InvalidArgumentException;
use JoliCode\Slack\Client;
use JoliCode\Slack\ClientFactory;
use Psr\Http\Client\ClientInterface;

final class SlackNotificationSender implements NotificationSenderInterface
{
    /**
     * @var Client[]
     */
    private array $slackClients = [];

    public function __construct(private readonly ClientInterface $client)
    {
    }

    public static function getId(): string
    {
        return 'slack';
    }

    public static function getName(): string
    {
        return 'Slack';
    }

    public static function getConfigType(): string
    {
        return SlackConfigType::class;
    }

    public function send(NotificationSenderConfigInterface $config, string $message): void
    {
        if (!$config instanceof SlackConfig) {
            throw new InvalidArgumentException(sprintf('Config must be instance of %s', SlackConfig::class));
        }

        $client = $this->slackClients[$config->getApiKey()] ??= ClientFactory::create($config->getApiKey(), $this->client);

        $client->chatPostMessage([
            'channel' => 'general',
            'text' => sprintf('<!here> %s', $message),
        ]);
    }
}