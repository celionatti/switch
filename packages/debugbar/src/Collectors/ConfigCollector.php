<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class ConfigCollector extends AbstractCollector
{
    private array $customConfig = [];

    public function getName(): string
    {
        return 'config';
    }

    public function getTitle(): string
    {
        return 'Config';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>';
    }

    public function getBadge(): ?string
    {
        return 'PHP ' . PHP_VERSION;
    }

    public function setConfig(array $config): self
    {
        $this->customConfig = $config;
        return $this;
    }

    public function collect(): array
    {
        $opcacheEnabled = function_exists('opcache_get_status') && is_array(opcache_get_status(false));
        $opcacheStatus = $opcacheEnabled ? 'Enabled' : 'Disabled';

        // Collect and mask environment variables
        $env = [];
        $rawEnv = array_merge($_SERVER, $_ENV);
        $sensitiveKeys = ['key', 'secret', 'password', 'token', 'pass', 'auth', 'database_url', 'api_key', 'private'];

        foreach ($rawEnv as $key => $value) {
            $keyStr = (string) $key;
            $lower = strtolower($keyStr);

            $isSensitive = false;
            foreach ($sensitiveKeys as $pattern) {
                if (str_contains($lower, $pattern)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $env[$keyStr] = '•••••••• (Masked for Security)';
            } elseif (is_scalar($value) || is_null($value)) {
                $env[$keyStr] = $value;
            } else {
                $env[$keyStr] = '[' . gettype($value) . ']';
            }
        }

        ksort($env);

        return [
            'php_version' => PHP_VERSION,
            'switch_version' => '1.0.0',
            'os' => PHP_OS . ' (' . PHP_OS_FAMILY . ')',
            'sapi' => PHP_SAPI,
            'opcache' => $opcacheStatus,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'loaded_extensions' => get_loaded_extensions(),
            'extension_count' => count(get_loaded_extensions()),
            'environment' => $env,
            'custom_config' => $this->maskConfig($this->customConfig),
        ];
    }

    private function maskConfig(array $config): array
    {
        $masked = [];
        $sensitiveKeys = ['key', 'secret', 'password', 'token', 'pass', 'auth'];

        foreach ($config as $key => $value) {
            $lower = strtolower((string) $key);
            $isSensitive = false;
            foreach ($sensitiveKeys as $pat) {
                if (str_contains($lower, $pat)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $masked[$key] = '•••••••• (Masked)';
            } elseif (is_array($value)) {
                $masked[$key] = $this->maskConfig($value);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }
}
