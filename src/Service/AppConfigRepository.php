<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NoConfigException;
use App\Model\AppConfig;
use App\Model\ShinobiConfig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class AppConfigRepository
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws NoConfigException
     */
    public function get(): AppConfig
    {
        $path = $this->getJsonPath();

        if (!file_exists($path)) {
            throw new NoConfigException();
        }

        return $this->serializer->deserialize(file_get_contents($path), AppConfig::class, 'json');
    }

    private function getJsonPath(): string
    {
        return sprintf('%s/var/app_config.json', $this->parameterBag->get('kernel.project_dir'));
    }

    public function save(AppConfig $config): void
    {
        $serialized = $this->serializer->serialize($config, 'json');

        file_put_contents($this->getJsonPath(), $serialized);
    }

    public function createEmpty(): AppConfig
    {
        return new AppConfig(
            new ShinobiConfig('http', 'localhost', 8080, 'changeme', 'changeme'),
        );
    }
}