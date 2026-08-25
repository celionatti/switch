<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class CacheCollector extends AbstractCollector
{
    private int $hits = 0;
    private int $misses = 0;
    private int $writes = 0;
    private int $deletes = 0;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $operations = [];

    public function getName(): string
    {
        return 'cache';
    }

    public function getTitle(): string
    {
        return 'Cache';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>';
    }

    public function getBadge(): ?string
    {
        $total = $this->hits + $this->misses + $this->writes;
        if ($total === 0) {
            return null;
        }

        return $this->hits . ' hits / ' . $this->misses . ' miss';
    }

    public function logHit(string $key, mixed $value = null): self
    {
        $this->hits++;
        $this->operations[] = [
            'type' => 'hit',
            'key' => $key,
            'value' => $this->sanitizeValue($value, 2),
            'time' => microtime(true),
        ];
        return $this;
    }

    public function logMiss(string $key): self
    {
        $this->misses++;
        $this->operations[] = [
            'type' => 'miss',
            'key' => $key,
            'time' => microtime(true),
        ];
        return $this;
    }

    public function logWrite(string $key, mixed $value, ?int $ttl = null): self
    {
        $this->writes++;
        $this->operations[] = [
            'type' => 'write',
            'key' => $key,
            'ttl' => $ttl,
            'value' => $this->sanitizeValue($value, 2),
            'time' => microtime(true),
        ];
        return $this;
    }

    public function logDelete(string $key): self
    {
        $this->deletes++;
        $this->operations[] = [
            'type' => 'delete',
            'key' => $key,
            'time' => microtime(true),
        ];
        return $this;
    }

    public function collect(): array
    {
        $totalReads = $this->hits + $this->misses;
        $hitRatio = $totalReads > 0 ? round(($this->hits / $totalReads) * 100, 1) : 0.0;

        return [
            'hits' => $this->hits,
            'misses' => $this->misses,
            'writes' => $this->writes,
            'deletes' => $this->deletes,
            'total_operations' => count($this->operations),
            'hit_ratio' => $hitRatio,
            'operations' => $this->operations,
        ];
    }

    public function reset(): void
    {
        $this->hits = 0;
        $this->misses = 0;
        $this->writes = 0;
        $this->deletes = 0;
        $this->operations = [];
    }
}
