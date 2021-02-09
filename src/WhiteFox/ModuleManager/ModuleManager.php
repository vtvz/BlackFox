<?php
declare(strict_types=1);

namespace WhiteFox\ModuleManager;

use Psr\Container\ContainerInterface;
use WhiteFox\ModuleManager\Provider\ConfigProviderHandler;
use WhiteFox\ModuleManager\Provider\ConfigProviderInterface;

class ModuleManager
{
    private const DEFAULT_PROVIDERS = [
        ConfigProviderInterface::class => ConfigProviderHandler::class,
    ];

    /**
     * @param ContainerInterface  $container
     * @param array<class-string> $providers
     */
    public function __construct(private ContainerInterface $container, private array $providers = [])
    {
        $this->providers = array_merge(static::DEFAULT_PROVIDERS, $providers);
    }

    /**
     * @param array<class-string<ModuleInterface>, array> $modules
     */
    public function load(array $modules): void
    {
        foreach ($modules as $class => $config) {
            /** @var ModuleInterface $module */
            $module = $this->container->get($class);

            foreach ($this->providers as $interface => $handlerClass) {
                if ($module instanceof $interface) {
                    $handler = $this->container->get($handlerClass);

                    $handler($module, $config);
                }
            }

            $module->register();
        }
    }
}
