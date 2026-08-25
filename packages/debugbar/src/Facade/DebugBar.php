<?php

declare(strict_types=1);

namespace Switch\DebugBar\Facade;

use Switch\DebugBar\Collectors\CollectorInterface;
use Switch\DebugBar\DebugBar as BaseDebugBar;

/**
 * Static Facade for Switch DebugBar.
 */
class DebugBar
{
    public static function getFacadeRoot(): BaseDebugBar
    {
        return BaseDebugBar::getInstance();
    }

    public static function enable(): BaseDebugBar
    {
        return static::getFacadeRoot()->enable();
    }

    public static function disable(): BaseDebugBar
    {
        return static::getFacadeRoot()->disable();
    }

    public static function isEnabled(): bool
    {
        return static::getFacadeRoot()->isEnabled();
    }

    public static function startMeasure(string $name, ?string $label = null): BaseDebugBar
    {
        return static::getFacadeRoot()->startMeasure($name, $label);
    }

    public static function stopMeasure(string $name): BaseDebugBar
    {
        return static::getFacadeRoot()->stopMeasure($name);
    }

    public static function measure(string $label, callable $callback, ?string $name = null): mixed
    {
        return static::getFacadeRoot()->measure($label, $callback, $name);
    }

    public static function addMessage(mixed $message, string $level = 'info'): BaseDebugBar
    {
        return static::getFacadeRoot()->addMessage($message, $level);
    }

    public static function debug(mixed $message): BaseDebugBar
    {
        return static::getFacadeRoot()->debug($message);
    }

    public static function info(mixed $message): BaseDebugBar
    {
        return static::getFacadeRoot()->info($message);
    }

    public static function warning(mixed $message): BaseDebugBar
    {
        return static::getFacadeRoot()->warning($message);
    }

    public static function error(mixed $message): BaseDebugBar
    {
        return static::getFacadeRoot()->error($message);
    }

    public static function addCollector(CollectorInterface $collector): BaseDebugBar
    {
        return static::getFacadeRoot()->addCollector($collector);
    }

    public static function getCollector(string $name): ?CollectorInterface
    {
        return static::getFacadeRoot()->getCollector($name);
    }

    public static function render(): string
    {
        return static::getFacadeRoot()->render();
    }

    public static function renderJson(): string
    {
        return static::getFacadeRoot()->renderJson();
    }

    public static function __callStatic(string $method, array $args): mixed
    {
        return static::getFacadeRoot()->$method(...$args);
    }
}
