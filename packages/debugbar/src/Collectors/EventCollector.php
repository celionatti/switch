<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class EventCollector extends AbstractCollector
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $events = [];

    public function getName(): string
    {
        return 'events';
    }

    public function getTitle(): string
    {
        return 'Events';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->events);
        return $count > 0 ? (string) $count : null;
    }

    public function logEvent(string|object $event, array $listeners = [], float $durationMs = 0.0): self
    {
        $name = is_object($event) ? get_class($event) : $event;

        $listenerNames = [];
        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $listenerNames[] = $listener;
            } elseif (is_array($listener)) {
                $listenerNames[] = (is_object($listener[0]) ? get_class($listener[0]) : $listener[0]) . '@' . $listener[1];
            } elseif ($listener instanceof \Closure) {
                $ref = new \ReflectionFunction($listener);
                $listenerNames[] = 'Closure (' . basename((string) $ref->getFileName()) . ':' . $ref->getStartLine() . ')';
            } elseif (is_object($listener)) {
                $listenerNames[] = get_class($listener);
            }
        }

        $this->events[] = [
            'name' => $name,
            'short_name' => basename(str_replace('\\', '/', $name)),
            'listeners' => $listenerNames,
            'listener_count' => count($listenerNames),
            'duration_ms' => round($durationMs, 2),
            'time' => microtime(true),
        ];

        return $this;
    }

    public function collect(): array
    {
        return [
            'count' => count($this->events),
            'events' => $this->events,
        ];
    }

    public function reset(): void
    {
        $this->events = [];
    }
}
