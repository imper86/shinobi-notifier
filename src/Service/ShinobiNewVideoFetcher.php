<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NoConfigException;
use App\Model\AppConfig;
use App\Model\ShinobiApi\Video;
use DateTimeImmutable;
use EmptyIterator;
use Psr\Http\Client\ClientExceptionInterface;
use Traversable;

final class ShinobiNewVideoFetcher
{
    public function __construct(
        private readonly ShinobiApi $shinobiApi,
        private readonly AppConfigRepository $configRepository
    ) {
    }

    /**
     * @return Traversable<int, Video>
     * @throws ClientExceptionInterface|NoConfigException
     */
    public function fetch(): Traversable
    {
        $config = $this->configRepository->get();

        if (null === $config) {
            return new EmptyIterator();
        }

        $newerThan = $this->createNewerThan($config);
        $lastVideoAppearedAt = null;

        $response = $this->shinobiApi->getVideos($newerThan);
        
        foreach ($response->getVideos() as $video) {
            if ($video->time > $newerThan) {
                $lastVideoAppearedAt = $video->time;

                yield $video;
            }
        }

        $config->lastVideoAppearedAt = $lastVideoAppearedAt ?? $newerThan;

        $this->configRepository->save($config);
    }

    private function createNewerThan(AppConfig $config): DateTimeImmutable
    {
        $newerThan = $config->lastVideoAppearedAt;
        $minNewerThan = new DateTimeImmutable('-5 minutes');

        return (null === $newerThan || $newerThan < $minNewerThan) ? $minNewerThan : $newerThan;
    }
}