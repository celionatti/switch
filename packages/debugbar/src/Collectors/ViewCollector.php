<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Switch\DebugBar\Data\ViewData;

class ViewCollector extends AbstractCollector
{
    /**
     * @var array<int, ViewData>
     */
    private array $views = [];

    public function getName(): string
    {
        return 'views';
    }

    public function getTitle(): string
    {
        return 'Views';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->views);
        if ($count === 0) {
            return '0';
        }

        $totalTime = $this->getTotalRenderTimeMs();
        return $count . ($totalTime > 0 ? ' (' . round($totalTime, 1) . 'ms)' : '');
    }

    public function addView(string $name, string $path, float $renderTimeMs = 0.0, array $data = []): self
    {
        $sanitizedData = $this->sanitizeValue($data, 2);
        $this->views[] = new ViewData($name, $path, $renderTimeMs, is_array($sanitizedData) ? $sanitizedData : []);
        return $this;
    }

    public function getTotalRenderTimeMs(): float
    {
        $total = 0.0;
        foreach ($this->views as $view) {
            $total += $view->renderTimeMs;
        }
        return $total;
    }

    public function collect(): array
    {
        $collected = [];
        foreach ($this->views as $v) {
            $collected[] = $v->toArray();
        }

        $totalMs = $this->getTotalRenderTimeMs();

        return [
            'count' => count($this->views),
            'total_render_time_ms' => round($totalMs, 2),
            'views' => $collected,
        ];
    }

    public function reset(): void
    {
        $this->views = [];
    }
}
