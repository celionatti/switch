<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

/**
 * Contract for all data collectors in Switch DebugBar.
 */
interface CollectorInterface
{
    /**
     * Unique identifier for the collector (e.g., 'time', 'queries', 'views').
     */
    public function getName(): string;

    /**
     * Human-readable title displayed in the tab bar.
     */
    public function getTitle(): string;

    /**
     * SVG icon markup or identifier for the tab.
     */
    public function getIcon(): string;

    /**
     * Quick summary badge text displayed on the tab pill (e.g., '14.2ms', '4 queries', '12').
     */
    public function getBadge(): ?string;

    /**
     * Status indicator color code for badge ('default', 'success', 'warning', 'danger', 'info', 'neon').
     */
    public function getBadgeColor(): string;

    /**
     * Collect and return structured data for the debug bar renderer.
     *
     * @return array<string, mixed>
     */
    public function collect(): array;

    /**
     * Determine if this collector has meaningful data to display.
     */
    public function isAvailable(): bool;

    /**
     * Reset collector state for new request or AJAX cycle.
     */
    public function reset(): void;
}
