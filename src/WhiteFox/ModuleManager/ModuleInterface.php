<?php
declare(strict_types=1);

namespace WhiteFox\ModuleManager;

interface ModuleInterface
{
    public function register(): void;
}
