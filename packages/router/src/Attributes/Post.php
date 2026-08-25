<?php

declare(strict_types=1);

namespace Switch\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Post extends Route
{
    public function __construct(
        string $path,
        ?string $name = null,
        array $middleware = []
    ) {
        parent::__construct($path, 'POST', $name, $middleware);
    }
}
