<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class HistoryCollector extends AbstractCollector
{
    private string $currentRequestId;
    private array $requests = [];

    public function __construct(string $currentRequestId)
    {
        $this->currentRequestId = $currentRequestId;
    }

    public function getName(): string
    {
        return 'history';
    }

    public function getTitle(): string
    {
        return 'History / AJAX';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 8 14"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->requests);
        return $count > 0 ? (string) $count : null;
    }

    public function setRequests(array $requests): self
    {
        $this->requests = $requests;
        return $this;
    }

    public function collect(): array
    {
        return [
            'current_id' => $this->currentRequestId,
            'count' => count($this->requests),
            'requests' => $this->requests,
        ];
    }

    public function reset(): void
    {
        $this->requests = [];
    }
}
