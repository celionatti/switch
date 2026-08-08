<?php

declare(strict_types=1);

namespace Switch\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string, array<int, callable>> Event FQCN => list of listener callables
     */
    private array $listeners = [];

    public function addListener(string $eventType, callable $listener): self
    {
        $this->listeners[$eventType][] = $listener;
        return $this;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = get_class($event);
        $listeners = [];

        foreach ($this->listeners as $type => $typeListeners) {
            if ($eventClass === $type || is_subclass_of($event, $type) || (interface_exists($type) && is_a($event, $type))) {
                foreach ($typeListeners as $listener) {
                    $listeners[] = $listener;
                }
            }
        }

        return $listeners;
    }
}
