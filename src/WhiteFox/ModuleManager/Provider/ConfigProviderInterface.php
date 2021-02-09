<?php
declare(strict_types=1);

namespace WhiteFox\ModuleManager\Provider;

use WhiteFox\ConfigManager\ConfigManager;

interface ConfigProviderInterface
{
    /**
     * @return array<callable>
     */
    public function getConfigProviders(): array;

    public function getConfig(): ConfigManager;

    public function setConfig(ConfigManager $configManager): static;
}
