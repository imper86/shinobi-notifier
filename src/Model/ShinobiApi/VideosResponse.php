<?php

declare(strict_types=1);

namespace App\Model\ShinobiApi;

final class VideosResponse
{
    public function __construct(
        private readonly bool $ok,
        private array $videos,
    )
    {
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function addVideo(Video $video): void
    {
        $this->videos[] = $video;
    }

    /**
     * @return Video[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }
}