<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

/**
 * Base class providing shared formatting, sanitization, and state utilities for collectors.
 */
abstract class AbstractCollector implements CollectorInterface
{
    protected bool $enabled = true;

    public function isAvailable(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getBadgeColor(): string
    {
        return 'default';
    }

    public function reset(): void
    {
        // Default empty reset
    }

    /**
     * Format memory bytes into human-friendly string (e.g., 2.45 MB).
     */
    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return round($value, $precision) . ' ' . $units[$power];
    }

    /**
     * Format milliseconds / seconds into human-readable duration (e.g., 4.23 ms or 1.25 s).
     */
    protected function formatDuration(float $seconds, int $precision = 2): string
    {
        $ms = $seconds * 1000;
        if ($ms < 1000) {
            return round($ms, $precision) . ' ms';
        }

        return round($seconds, $precision) . ' s';
    }

    /**
     * Recursively sanitize values to prevent circular references or memory blowups.
     */
    protected function sanitizeValue(mixed $value, int $maxDepth = 3, int $currentDepth = 0): mixed
    {
        if ($currentDepth >= $maxDepth) {
            return is_object($value) ? '[' . get_class($value) . ']' : (is_array($value) ? '[Array(' . count($value) . ')]' : $value);
        }

        if (is_null($value) || is_scalar($value)) {
            return $value;
        }

        if (is_array($value)) {
            $result = [];
            $count = 0;
            foreach ($value as $k => $v) {
                if ($count++ > 50) {
                    $result['...'] = '(' . (count($value) - 50) . ' more items)';
                    break;
                }
                $result[$k] = $this->sanitizeValue($v, $maxDepth, $currentDepth + 1);
            }
            return $result;
        }

        if (is_object($value)) {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d H:i:s.u');
            }
            if ($value instanceof \Throwable) {
                return [
                    'class' => get_class($value),
                    'message' => $value->getMessage(),
                    'file' => $value->getFile() . ':' . $value->getLine(),
                ];
            }
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
            if (method_exists($value, 'toArray')) {
                return $this->sanitizeValue($value->toArray(), $maxDepth, $currentDepth + 1);
            }

            return '[Object: ' . get_class($value) . ']';
        }

        if (is_resource($value)) {
            return '[Resource: ' . get_resource_type($value) . ']';
        }

        return '[Unknown Type]';
    }
}
