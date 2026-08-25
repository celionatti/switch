<?php

declare(strict_types=1);

namespace Switch\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param string $path
     * @param string|array<string> $methods
     * @param string|null $name
     * @param array<int, mixed> $middleware
     */
    public function __construct(
        public string $path,
        public string|array $methods = 'GET',
        public ?string $name = null,
        public array $middleware = []
    ) {
    }
}
