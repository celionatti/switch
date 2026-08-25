<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Switch\DebugBar\Dumper\HtmlDumper;

class LogCollector extends AbstractCollector
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $messages = [];

    public function getName(): string
    {
        return 'logs';
    }

    public function getTitle(): string
    {
        return 'Messages';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    }

    public function getBadge(): ?string
    {
        $count = count($this->messages);
        return (string) $count;
    }

    public function getBadgeColor(): string
    {
        $hasError = false;
        $hasWarning = false;

        foreach ($this->messages as $m) {
            $lvl = strtolower((string) $m['level']);
            if (in_array($lvl, ['error', 'critical', 'alert', 'emergency'], true)) {
                $hasError = true;
                break;
            }
            if ($lvl === 'warning') {
                $hasWarning = true;
            }
        }

        if ($hasError) {
            return 'danger';
        }
        if ($hasWarning) {
            return 'warning';
        }
        if (count($this->messages) > 0) {
            return 'info';
        }

        return 'default';
    }

    /**
     * Add a message / dump to the log collector.
     */
    public function addMessage(mixed $message, string $level = 'info', ?string $file = null, ?int $line = null): self
    {
        if ($file === null) {
            $caller = $this->findCaller();
            $file = $caller['file'] ?? null;
            $line = $caller['line'] ?? null;
        }

        $htmlDump = null;
        $isString = is_string($message);
        $textMessage = $isString ? $message : null;

        if (!is_scalar($message) || is_bool($message) || is_null($message)) {
            $htmlDump = HtmlDumper::dump($message);
        }

        $this->messages[] = [
            'level' => strtolower($level),
            'message' => $textMessage,
            'dump' => $htmlDump,
            'raw_value' => $this->sanitizeValue($message, 3),
            'time' => microtime(true),
            'time_formatted' => date('H:i:s') . '.' . substr((string) microtime(), 2, 3),
            'file' => $file,
            'line' => $line,
            'caller' => $file ? basename($file) . ':' . $line : null,
        ];

        return $this;
    }

    public function debug(mixed $message): self
    {
        return $this->addMessage($message, 'debug');
    }

    public function info(mixed $message): self
    {
        return $this->addMessage($message, 'info');
    }

    public function warning(mixed $message): self
    {
        return $this->addMessage($message, 'warning');
    }

    public function error(mixed $message): self
    {
        return $this->addMessage($message, 'error');
    }

    public function collect(): array
    {
        return [
            'count' => count($this->messages),
            'messages' => $this->messages,
        ];
    }

    public function reset(): void
    {
        $this->messages = [];
    }

    private function findCaller(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        foreach ($trace as $frame) {
            $file = $frame['file'] ?? '';
            if (empty($file)) {
                continue;
            }

            if (
                !str_contains($file, '/DebugBar/') &&
                !str_contains($file, '\\DebugBar\\') &&
                !str_contains($file, 'helpers.php')
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
