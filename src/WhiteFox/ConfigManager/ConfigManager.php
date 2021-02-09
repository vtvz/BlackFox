<?php
declare(strict_types=1);

namespace WhiteFox\ConfigManager;

use Dflydev\DotAccessData\Data;
use Laminas\ConfigAggregator\ConfigAggregator;

class ConfigManager
{
    private Data $config;

    /**
     * @param array<callable> $providers
     */
    public function __construct(array $providers)
    {
        $aggregator = new ConfigAggregator($providers);

        $this->config = new Data($aggregator->getMergedConfig());
    }

    public function get(?string $key = null, mixed $default = null): mixed
    {
        if (null === $key) {
            return $this->config->export();
        }

        return $this->config->get($key, $default);
    }
}
