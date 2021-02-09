<?php
declare(strict_types=1);

namespace WhiteFox\Routing\Attribute;

use Attribute;
use JetBrains\PhpStorm\Immutable;

#[Attribute(Attribute::TARGET_CLASS)]
#[Immutable(Immutable::CONSTRUCTOR_WRITE_SCOPE)]
class Group
{
    public function __construct(public string $name, public string $pattern, public array $middlewares = [])
    {
    }
}
