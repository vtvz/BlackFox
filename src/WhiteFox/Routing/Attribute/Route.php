<?php
declare(strict_types=1);

namespace WhiteFox\Routing\Attribute;

use Attribute;
use JetBrains\PhpStorm\Immutable;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
#[Immutable(Immutable::CONSTRUCTOR_WRITE_SCOPE)]
class Route
{
    public function __construct(
        public string $name,
        public string $method,
        public string $pattern,
        public array $middlewares = [],
    ) {
    }
}
