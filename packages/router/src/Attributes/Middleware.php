<?php

declare(strict_types=1);

namespace Switch\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Middleware
{
    /**
     * @var array<int, mixed>
     */
    public array $middleware;

    public function __construct(string|array ...$middleware)
    {
        $flattened = [];
        foreach ($middleware as $m) {
            if (is_array($m)) {
                $flattened = array_merge($flattened, $m);
            } else {
                $flattened[] = $m;
            }
        }
        $this->middleware = $flattened;
    }
}
