<?php
declare(strict_types=1);

namespace WhiteFox\Routing\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Get extends Route
{
    public function __construct(string $name, string $pattern, array $middlewares = [])
    {
        parent::__construct($name, 'GET', $pattern, $middlewares);
    }
}
