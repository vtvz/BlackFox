<?php
declare(strict_types=1);

namespace SapiRunner;

use DI;
use SapiRunner\Http\Runner;
use WhiteFox\Http\RunnerInterface;
use WhiteFox\ModuleManager\AbstractModule;

class Module extends AbstractModule
{
    public function __construct(private DI\Container $container)
    {
    }

    public function register(): void
    {
        $this->container->set(RunnerInterface::class, DI\get(Runner::class));
    }
}
