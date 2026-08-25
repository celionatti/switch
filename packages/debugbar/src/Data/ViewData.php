<?php

declare(strict_types=1);

namespace Switch\DebugBar\Data;

class ViewData
{
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly float $renderTimeMs,
        public readonly array $data = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'render_time_ms' => round($this->renderTimeMs, 2),
            'param_count' => count($this->data),
            'data_keys' => array_keys($this->data),
            'data' => $this->data,
        ];
    }
}
