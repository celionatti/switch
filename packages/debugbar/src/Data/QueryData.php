<?php

declare(strict_types=1);

namespace Switch\DebugBar\Data;

class QueryData
{
    public function __construct(
        public readonly string $sql,
        public readonly array $bindings,
        public readonly float $timeMs,
        public readonly ?string $file = null,
        public readonly ?int $line = null,
        public readonly string $connection = 'default',
        public bool $isDuplicate = false,
        public int $duplicateCount = 1
    ) {
    }

    /**
     * Get SQL with bindings interpolated for visual inspection and debugging.
     */
    public function getInterpolatedSql(): string
    {
        $sql = $this->sql;
        if (empty($this->bindings)) {
            return $sql;
        }

        foreach ($this->bindings as $key => $binding) {
            $formatted = match (true) {
                is_null($binding) => 'NULL',
                is_bool($binding) => $binding ? '1' : '0',
                is_numeric($binding) => (string) $binding,
                $binding instanceof \DateTimeInterface => "'" . $binding->format('Y-m-d H:i:s') . "'",
                default => "'" . addslashes((string) $binding) . "'",
            };

            if (is_numeric($key)) {
                $sql = preg_replace('/\?/', $formatted, $sql, 1);
            } else {
                $placeholder = str_starts_with($key, ':') ? $key : ':' . $key;
                $sql = str_replace($placeholder, $formatted, $sql);
            }
        }

        return $sql;
    }

    public function toArray(): array
    {
        return [
            'sql' => $this->sql,
            'interpolated' => $this->getInterpolatedSql(),
            'bindings' => $this->bindings,
            'time_ms' => round($this->timeMs, 2),
            'file' => $this->file,
            'line' => $this->line,
            'caller' => $this->file ? basename($this->file) . ':' . $this->line : null,
            'connection' => $this->connection,
            'is_duplicate' => $this->isDuplicate,
            'duplicate_count' => $this->duplicateCount,
            'is_slow' => $this->timeMs > 50.0,
        ];
    }
}
