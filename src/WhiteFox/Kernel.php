<?php
declare(strict_types=1);

namespace WhiteFox;

use DI;
use Psr\Container\ContainerInterface;
use WhiteFox\ConfigManager\ConfigManager;
use WhiteFox\Http\RunnerInterface;
use WhiteFox\ModuleManager\ModuleManager;

class Kernel
{
    private ContainerInterface|DI\Container $container;

    public function __construct()
    {
        $containerBuilder = new DI\ContainerBuilder();
        $this->container  = $containerBuilder->build();
    }

    public function build(array $configProviders)
    {
        $this->container->set(ConfigManager::class, DI\create()->constructor($configProviders));

        $this->container->set(
            ModuleManager::class,
            DI\autowire()->constructorParameter(
                'providers',
                fn(ContainerInterface $container) => $container->get(ConfigManager::class)->get(
                    'moduleManager.providers',
                    []
                )
            )
        );
        $this->container
            ->get(ModuleManager::class)
            ->load($this->container->get(ConfigManager::class)->get('moduleManager.modules'));
    }

    public function run(): void
    {
        $this->container->call([$this->container->get(RunnerInterface::class), 'run']);
    }
}
