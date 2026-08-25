<?php

declare(strict_types=1);

use Switch\DebugBar\DebugBar;

if (!function_exists('debugbar')) {
    /**
     * Get the DebugBar instance.
     */
    function debugbar(): DebugBar
    {
        return DebugBar::getInstance();
    }
}

if (!function_exists('debug')) {
    /**
     * Add one or more variables/messages to the DebugBar Messages collector.
     */
    function debug(mixed ...$vars): mixed
    {
        $bar = DebugBar::getInstance();

        if ($bar->isEnabled()) {
            $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? [];
            $file = $caller['file'] ?? null;
            $line = $caller['line'] ?? null;

            foreach ($vars as $var) {
                if ($bar->hasCollector('logs')) {
                    $bar->getCollector('logs')?->addMessage($var, 'debug', $file, $line);
                }
            }
        }

        return count($vars) === 1 ? $vars[0] : $vars;
    }
}

if (!function_exists('debugbar_measure')) {
    /**
     * Measure the execution duration and memory of a callable block.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    function debugbar_measure(string $label, callable $callback, ?string $name = null): mixed
    {
        return DebugBar::getInstance()->measure($label, $callback, $name);
    }
}

if (!function_exists('debugbar_start_measure')) {
    /**
     * Start a timeline measurement checkpoint.
     */
    function debugbar_start_measure(string $name, ?string $label = null): void
    {
        DebugBar::getInstance()->startMeasure($name, $label);
    }
}

if (!function_exists('debugbar_stop_measure')) {
    /**
     * Stop a timeline measurement checkpoint.
     */
    function debugbar_stop_measure(string $name): void
    {
        DebugBar::getInstance()->stopMeasure($name);
    }
}

if (!function_exists('debugbar_log')) {
    /**
     * Log a message or error to the DebugBar.
     */
    function debugbar_log(mixed $message, string $level = 'info'): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? [];
        $file = $caller['file'] ?? null;
        $line = $caller['line'] ?? null;

        $bar = DebugBar::getInstance();
        if ($bar->isEnabled() && $bar->hasCollector('logs')) {
            $bar->getCollector('logs')?->addMessage($message, $level, $file, $line);
        }
    }
}

if (!function_exists('debugbar_render')) {
    /**
     * Render the DebugBar HTML manually (useful in custom templates).
     */
    function debugbar_render(): string
    {
        return DebugBar::getInstance()->render();
    }
}
