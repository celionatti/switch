<?php

declare(strict_types=1);

namespace Switch\Event\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;
use Switch\Event\EventDispatcher;
use Switch\Event\ListenerProvider;
use Switch\Event\StoppableEventTrait;

class SampleEvent
{
    public array $log = [];
}

class CustomStoppableEvent implements StoppableEventInterface
{
    use StoppableEventTrait;

    public int $counter = 0;
}

class EventDispatcherTest extends TestCase
{
    public function testEventDispatchingToListeners(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SampleEvent::class, function (SampleEvent $event) {
            $event->log[] = 'first';
        });
        $provider->addListener(SampleEvent::class, function (SampleEvent $event) {
            $event->log[] = 'second';
        });

        $dispatcher = new EventDispatcher($provider);
        $event = new SampleEvent();
        $dispatched = $dispatcher->dispatch($event);

        $this->assertSame($event, $dispatched);
        $this->assertEquals(['first', 'second'], $event->log);
    }

    public function testStoppableEventHaltsPropagation(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(CustomStoppableEvent::class, function (CustomStoppableEvent $event) {
            $event->counter += 1;
            $event->stopPropagation();
        });
        $provider->addListener(CustomStoppableEvent::class, function (CustomStoppableEvent $event) {
            $event->counter += 10;
        });

        $dispatcher = new EventDispatcher($provider);
        $event = new CustomStoppableEvent();
        $dispatcher->dispatch($event);

        $this->assertEquals(1, $event->counter);
        $this->assertTrue($event->isPropagationStopped());
    }
}
