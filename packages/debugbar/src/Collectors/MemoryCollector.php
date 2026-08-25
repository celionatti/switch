<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class MemoryCollector extends AbstractCollector
{
    private int $startMemory;

    public function __construct()
    {
        $this->startMemory = memory_get_usage(true);
    }

    public function getName(): string
    {
        return 'memory';
    }

    public function getTitle(): string
    {
        return 'Memory';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 19v-3"/><path d="M10 19v-3"/><path d="M14 19v-3"/><path d="M18 19v-3"/><rect x="2" y="5" width="20" height="11" rx="2"/><line x1="6" y1="9" x2="6.01" y2="9"/><line x1="10" y1="9" x2="10.01" y2="9"/></svg>';
    }

    public function getBadge(): ?string
    {
        $peak = memory_get_peak_usage(true);
        return $this->formatBytes($peak);
    }

    public function getBadgeColor(): string
    {
        $peakMb = memory_get_peak_usage(true) / 1024 / 1024;
        if ($peakMb < 16) {
            return 'success';
        }
        if ($peakMb < 64) {
            return 'warning';
        }
        return 'danger';
    }

    public function collect(): array
    {
        $currentUsage = memory_get_usage(false);
        $currentAllocated = memory_get_usage(true);
        $peakUsage = memory_get_peak_usage(false);
        $peakAllocated = memory_get_peak_usage(true);
        $limitStr = ini_get('memory_limit') ?: '-1';
        $limitBytes = $this->parseMemoryLimit($limitStr);

        $percentOfLimit = null;
        if ($limitBytes > 0) {
            $percentOfLimit = round(($peakAllocated / $limitBytes) * 100, 1);
        }

        return [
            'peak_allocated' => $peakAllocated,
            'peak_allocated_formatted' => $this->formatBytes($peakAllocated),
            'peak_usage' => $peakUsage,
            'peak_usage_formatted' => $this->formatBytes($peakUsage),
            'current_allocated' => $currentAllocated,
            'current_allocated_formatted' => $this->formatBytes($currentAllocated),
            'current_usage' => $currentUsage,
            'current_usage_formatted' => $this->formatBytes($currentUsage),
            'start_memory' => $this->startMemory,
            'start_memory_formatted' => $this->formatBytes($this->startMemory),
            'memory_limit' => $limitStr,
            'memory_limit_bytes' => $limitBytes,
            'percent_of_limit' => $percentOfLimit,
        ];
    }

    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1' || $limit === '') {
            return -1;
        }

        $unit = strtolower(substr($limit, -1));
        $bytes = (int) substr($limit, 0, -1);

        return match ($unit) {
            'g' => $bytes * 1024 * 1024 * 1024,
            'm' => $bytes * 1024 * 1024,
            'k' => $bytes * 1024,
            default => (int) $limit,
        };
    }
}
