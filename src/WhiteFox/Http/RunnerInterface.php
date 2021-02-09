<?php
declare(strict_types=1);

namespace WhiteFox\Http;

use Psr\Http\Server\RequestHandlerInterface;

interface RunnerInterface
{
    public function run(RequestHandlerInterface $handler): void;
}
