<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Switch\DebugBar\Data\QueryData;

class QueryCollector extends AbstractCollector
{
    /**
     * @var array<int, QueryData>
     */
    private array $queries = [];

    private float $slowThresholdMs = 50.0;

    public function getName(): string
    {
        return 'queries';
    }

    public function getTitle(): string
    {
        return 'Queries';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->queries);
        if ($count === 0) {
            return '0';
        }

        $totalTime = $this->getTotalTimeMs();
        return $count . ' (' . round($totalTime, 1) . 'ms)';
    }

    public function getBadgeColor(): string
    {
        $count = count($this->queries);
        if ($count === 0) {
            return 'default';
        }

        $duplicates = $this->getDuplicateCount();
        $slowCount = $this->getSlowQueryCount();

        if ($slowCount > 0 || $duplicates > 2 || $count > 40) {
            return 'danger';
        }

        if ($duplicates > 0 || $count > 15) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Add an executed query to the collector.
     *
     * @param string $sql Raw SQL
     * @param array $bindings Bound query parameters
     * @param float $timeMs Execution duration in milliseconds
     * @param string $connection Connection name
     * @param string|null $file Originating file
     * @param int|null $line Originating line
     */
    public function addQuery(
        string $sql,
        array $bindings = [],
        float $timeMs = 0.0,
        string $connection = 'default',
        ?string $file = null,
        ?int $line = null
    ): self {
        if ($file === null) {
            $caller = $this->findCaller();
            $file = $caller['file'] ?? null;
            $line = $caller['line'] ?? null;
        }

        $this->queries[] = new QueryData(
            sql: $sql,
            bindings: $bindings,
            timeMs: $timeMs,
            file: $file,
            line: $line,
            connection: $connection
        );

        return $this;
    }

    public function setSlowThresholdMs(float $threshold): self
    {
        $this->slowThresholdMs = $threshold;
        return $this;
    }

    public function getTotalTimeMs(): float
    {
        $total = 0.0;
        foreach ($this->queries as $q) {
            $total += $q->timeMs;
        }
        return $total;
    }

    public function getSlowQueryCount(): int
    {
        $count = 0;
        foreach ($this->queries as $q) {
            if ($q->timeMs >= $this->slowThresholdMs) {
                $count++;
            }
        }
        return $count;
    }

    public function getDuplicateCount(): int
    {
        $grouped = [];
        $duplicates = 0;

        foreach ($this->queries as $q) {
            $key = $q->sql . ':' . json_encode($q->bindings);
            $grouped[$key] = ($grouped[$key] ?? 0) + 1;
        }

        foreach ($grouped as $count) {
            if ($count > 1) {
                $duplicates += ($count - 1);
            }
        }

        return $duplicates;
    }

    public function collect(): array
    {
        $queryMap = [];
        foreach ($this->queries as $q) {
            $key = $q->sql . ':' . json_encode($q->bindings);
            $queryMap[$key] = ($queryMap[$key] ?? 0) + 1;
        }

        $collected = [];
        foreach ($this->queries as $q) {
            $key = $q->sql . ':' . json_encode($q->bindings);
            $dupCount = $queryMap[$key] ?? 1;
            $q->isDuplicate = $dupCount > 1;
            $q->duplicateCount = $dupCount;
            $collected[] = $q->toArray();
        }

        $totalMs = $this->getTotalTimeMs();

        return [
            'count' => count($this->queries),
            'total_time_ms' => round($totalMs, 2),
            'total_time_formatted' => $this->formatDuration($totalMs / 1000),
            'slow_count' => $this->getSlowQueryCount(),
            'duplicate_count' => $this->getDuplicateCount(),
            'queries' => $collected,
        ];
    }

    public function reset(): void
    {
        $this->queries = [];
    }

    /**
     * Find originating application file/line from backtrace.
     */
    private function findCaller(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        foreach ($trace as $frame) {
            $file = $frame['file'] ?? '';
            if (empty($file)) {
                continue;
            }

            // Skip internal packages and vendor classes
            if (
                !str_contains($file, '/DebugBar/') &&
                !str_contains($file, '\\DebugBar\\') &&
                !str_contains($file, '/database/src/') &&
                !str_contains($file, '\\database\\src\\') &&
                !str_contains($file, '/vendor/')
            ) {
                return [
                    'file' => $file,
                    'line' => $frame['line'] ?? null,
                ];
            }
        }

        return ['file' => null, 'line' => null];
    }
}
