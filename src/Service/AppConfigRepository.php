<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EmptyConfigException;
use App\Exception\NoConfigException;
use App\Model\AppConfig;
use App\Model\ShinobiConfig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class AppConfigRepository
{
    private ?AppConfig $config = null;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws NoConfigException
     * @throws EmptyConfigException
     */
    public function get(bool $throwOnEmpty = true): AppConfig
    {
        if (null !== $this->config) {
            return $this->config;
        }

        $path = $this->getJsonPath();

        if (!$this->exists()) {
            throw new NoConfigException();
        }

        $this->config = $this->serializer->deserialize(file_get_contents($path), AppConfig::class, 'json');

        if ($throwOnEmpty && $this->config->isEmpty) {
            throw new EmptyConfigException();
        }

        return $this->config;
    }

    public function save(AppConfig $config): void
    {
        $this->config = $config;

        $serialized = $this->serializer->serialize($config, 'json');
        $filePath = $this->getJsonPath();
        $fileDir = dirname($filePath);

        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0755, true);
        }

        file_put_contents($filePath, $serialized);
    }

    public function createEmpty(): AppConfig
    {
        return new AppConfig(
            new ShinobiConfig('http', 'host-or-ip-address', 8080, 'changeme', 'changeme'),
        );
    }

    public function exists(): bool
    {
        return file_exists($this->getJsonPath());
    }

    private function getJsonPath(): string
    {
        return sprintf('%s/var/config/app_config.json', $this->parameterBag->get('kernel.project_dir'));
    }
}