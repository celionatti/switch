<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class SessionCollector extends AbstractCollector
{
    private array $data = [];
    private ?string $id = null;

    public function getName(): string
    {
        return 'session';
    }

    public function getTitle(): string
    {
        return 'Session';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->data);
        return $count > 0 ? (string) $count : null;
    }

    public function setSessionData(array $data, ?string $id = null): self
    {
        $this->data = $data;
        $this->id = $id;
        return $this;
    }

    public function collect(): array
    {
        // Try automatically fetching from global $_SESSION or Switch Session if available
        $data = $this->data;
        $id = $this->id;

        if (empty($data) && isset($_SESSION) && is_array($_SESSION)) {
            $data = $_SESSION;
            $id = session_id() ?: null;
        }

        if (empty($data) && class_exists(\Switch\Session\Session::class)) {
            try {
                if (\Switch\Session\Session::isStarted()) {
                    $data = \Switch\Session\Session::all();
                    $id = \Switch\Session\Session::getId();
                }
            } catch (\Throwable) {
                // Ignore
            }
        }

        return [
            'id' => $id,
            'count' => count($data),
            'attributes' => $this->sanitizeValue($data),
        ];
    }

    public function reset(): void
    {
        $this->data = [];
        $this->id = null;
    }
}
