<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\ShinobiApi\Video;
use DateTimeImmutable;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Traversable;

final class ShinobiNewVideoFetcher
{
    public function __construct(
        private readonly ShinobiApi $shinobiApi,
        private readonly TimestampRepository $timestampRepository,
    ) {
    }

    /**
     * @return Traversable<int, Video>
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function fetch(): Traversable
    {
        $newerThan = $this->createNewerThan();
        $lastVideoAppearedAt = null;

        $response = $this->shinobiApi->getVideos($newerThan);
        
        foreach ($response->getVideos() as $video) {
            if ($video->time > $newerThan) {
                $lastVideoAppearedAt = $video->time;

                yield $video;
            }
        }

        $this->timestampRepository->save('video_nt', $lastVideoAppearedAt ?? $newerThan);
    }

    /**
     * @throws Exception
     */
    private function createNewerThan(): DateTimeImmutable
    {
        $newerThan = $this->timestampRepository->load('video_nt');
        $minNewerThan = new DateTimeImmutable('-5 minutes');

        return (null === $newerThan || $newerThan < $minNewerThan) ? $minNewerThan : $newerThan;
    }
}