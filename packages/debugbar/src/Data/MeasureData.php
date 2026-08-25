<?php

declare(strict_types=1);

namespace Switch\DebugBar\Data;

class MeasureData
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly float $startTime,
        public ?float $endTime = null,
        public readonly int $memoryStart = 0,
        public ?int $memoryEnd = null
    ) {
    }

    public function stop(?float $endTime = null, ?int $memoryEnd = null): void
    {
        $this->endTime = $endTime ?? microtime(true);
        $this->memoryEnd = $memoryEnd ?? memory_get_usage(true);
    }

    public function getDurationMs(): float
    {
        $end = $this->endTime ?? microtime(true);
        return ($end - $this->startTime) * 1000;
    }

    public function getMemoryDelta(): int
    {
        if ($this->memoryEnd === null) {
            return 0;
        }
        return max(0, $this->memoryEnd - $this->memoryStart);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'start' => $this->startTime,
            'end' => $this->endTime,
            'duration_ms' => round($this->getDurationMs(), 2),
            'memory_delta' => $this->getMemoryDelta(),
        ];
    }
}
