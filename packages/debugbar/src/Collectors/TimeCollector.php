<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Switch\DebugBar\Data\MeasureData;

class TimeCollector extends AbstractCollector
{
    private float $requestStartTime;
    private ?float $requestEndTime = null;
    private ?float $bootDuration = null;

    /**
     * @var array<string, MeasureData>
     */
    private array $measures = [];

    public function __construct(?float $requestStartTime = null)
    {
        $this->requestStartTime = $requestStartTime
            ?? (isset($_SERVER['REQUEST_TIME_FLOAT']) ? (float) $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true));
    }

    public function getName(): string
    {
        return 'time';
    }

    public function getTitle(): string
    {
        return 'Timeline';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
    }

    public function getBadge(): ?string
    {
        $duration = $this->getTotalDuration();
        return $this->formatDuration($duration);
    }

    public function getBadgeColor(): string
    {
        $ms = $this->getTotalDuration() * 1000;
        if ($ms < 100) {
            return 'success';
        }
        if ($ms < 300) {
            return 'warning';
        }
        return 'danger';
    }

    public function startMeasure(string $name, ?string $label = null): self
    {
        $this->measures[$name] = new MeasureData(
            name: $name,
            label: $label ?? ucfirst($name),
            startTime: microtime(true),
            memoryStart: memory_get_usage(true)
        );
        return $this;
    }

    public function stopMeasure(string $name): self
    {
        if (isset($this->measures[$name])) {
            $this->measures[$name]->stop(microtime(true), memory_get_usage(true));
        }
        return $this;
    }

    public function measure(string $label, callable $callback, ?string $name = null): mixed
    {
        $name ??= 'measure_' . uniqid('', true);
        $this->startMeasure($name, $label);

        try {
            return $callback();
        } finally {
            $this->stopMeasure($name);
        }
    }

    public function setBootDuration(float $seconds): self
    {
        $this->bootDuration = $seconds;
        return $this;
    }

    public function setRequestEndTime(float $endTime): self
    {
        $this->requestEndTime = $endTime;
        return $this;
    }

    public function getTotalDuration(): float
    {
        $end = $this->requestEndTime ?? microtime(true);
        return max(0.0001, $end - $this->requestStartTime);
    }

    public function collect(): array
    {
        $totalDuration = $this->getTotalDuration();
        $totalMs = $totalDuration * 1000;

        $measuresData = [];
        foreach ($this->measures as $measure) {
            $data = $measure->toArray();
            $durationMs = $data['duration_ms'];
            $data['percent'] = $totalMs > 0 ? min(100, round(($durationMs / $totalMs) * 100, 1)) : 0;
            $data['relative_start_ms'] = round(($measure->startTime - $this->requestStartTime) * 1000, 2);
            $measuresData[] = $data;
        }

        return [
            'start_time' => $this->requestStartTime,
            'end_time' => $this->requestEndTime ?? microtime(true),
            'duration' => $totalDuration,
            'duration_ms' => round($totalMs, 2),
            'duration_formatted' => $this->formatDuration($totalDuration),
            'boot_duration' => $this->bootDuration !== null ? round($this->bootDuration * 1000, 2) : null,
            'measures' => $measuresData,
            'measure_count' => count($measuresData),
        ];
    }

    public function reset(): void
    {
        $this->requestStartTime = microtime(true);
        $this->requestEndTime = null;
        $this->measures = [];
    }
}
