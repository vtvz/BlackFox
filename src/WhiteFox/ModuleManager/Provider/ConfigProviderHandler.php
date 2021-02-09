<?php
declare(strict_types=1);

namespace WhiteFox\ModuleManager\Provider;

use Laminas\ConfigAggregator\ArrayProvider;
use WhiteFox\ConfigManager\ConfigManager;

class ConfigProviderHandler
{
    public function __invoke(ConfigProviderInterface $module, array $config)
    {
        $providers   = $module->getConfigProviders();
        $providers[] = new ArrayProvider($config);

        $configManager = new ConfigManager($providers);

        $module->setConfig($configManager);
    }
}
