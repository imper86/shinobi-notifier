<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EmptyConfigException;
use App\Exception\NoConfigException;
use App\Exception\ShinobiApiException;
use App\Model\ShinobiApi\Monitor;
use App\Model\ShinobiApi\VideosResponse;
use App\Model\ShinobiConfig;
use DateTimeInterface;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ShinobiApi
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly UriFactoryInterface $uriFactory,
        private readonly SerializerInterface $serializer,
        private readonly AppConfigRepository $appConfigRepository,
        private readonly DateTimeConverter $timeConverter,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws EmptyConfigException
     * @throws NoConfigException
     * @throws ShinobiApiException
     */
    public function testConnection(): void
    {
        $uri = $this->createResourceUri('monitor');
        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->client->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new ShinobiApiException();
        }
    }

    /**
     * @return Monitor[]
     * @throws ClientExceptionInterface
     * @throws EmptyConfigException
     * @throws NoConfigException
     * @throws ShinobiApiException
     */
    public function getMonitors(): array
    {
        $uri = $this->createResourceUri('monitor');

        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->client->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new ShinobiApiException(sprintf('Bad response: %d', $response->getStatusCode()));
        }

        return $this->serializer->deserialize(
            $response->getBody()->__toString(),
            sprintf('%s[]', Monitor::class),
            'json'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function getVideos(?DateTimeInterface $newerThan = null): VideosResponse
    {
        $uri = $this->createResourceUri('videos');

        if ($newerThan) {
            $newerThan = $this->timeConverter->toUtcImmutable($newerThan);
            $uri = $uri->withQuery(http_build_query(['start' => $newerThan->format('Y-m-d\TH:i:s')]));
        }

        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->client->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new ShinobiApiException(sprintf('Bad response: %d', $response->getStatusCode()));
        }

        return $this->serializer->deserialize($response->getBody()->__toString(), VideosResponse::class, 'json');
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    public function getBasePath(): string
    {
        $config = $this->getConfig();

        return sprintf('%s://%s:%d', $config->schema, $config->host, $config->port);
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    private function createResourceUri(string $resource): UriInterface
    {
        $config = $this->getConfig();

        return $this->uriFactory->createUri(
            sprintf(
                '%s/%s/%s/%s',
                $this->getBasePath(),
                $config->apiKey,
                $resource,
                $config->groupKey,
            ),
        );
    }

    /**
     * @throws EmptyConfigException
     * @throws NoConfigException
     */
    private function getConfig(): ShinobiConfig
    {
        return $this->appConfigRepository->get()->shinobi;
    }
}