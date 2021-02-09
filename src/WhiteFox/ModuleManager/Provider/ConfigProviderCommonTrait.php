<?php
declare(strict_types=1);

namespace WhiteFox\ModuleManager\Provider;

use Laminas\ConfigAggregator\PhpFileProvider;
use ReflectionClass;
use WhiteFox\ConfigManager\ConfigManager;

trait ConfigProviderCommonTrait
{
    private ConfigManager $config;

    public function getConfigProviders(): array
    {
        $class = new ReflectionClass($this::class);

        $glob = dirname($class->getFileName()) . '/../config/*.php';

        return [new PhpFileProvider($glob)];
    }

    public function getConfig(): ConfigManager
    {
        return $this->config;
    }

    public function setConfig(ConfigManager $configManager): static
    {
        $this->config = $configManager;

        return $this;
    }
}
