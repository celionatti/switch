<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Switch\DebugBar\Collectors\TimeCollector;
use Switch\DebugBar\DebugBar;
use Switch\DebugBar\Facade\DebugBar as DebugBarFacade;

class DebugBarTest extends TestCase
{
    protected function setUp(): void
    {
        DebugBar::setInstance(null);
    }

    public function testSingletonInstance(): void
    {
        $bar1 = DebugBar::getInstance();
        $bar2 = DebugBar::getInstance();

        $this->assertSame($bar1, $bar2);
    }

    public function testEnableAndDisable(): void
    {
        $bar = DebugBar::getInstance();
        $this->assertTrue($bar->isEnabled());

        $bar->disable();
        $this->assertFalse($bar->isEnabled());

        $bar->enable();
        $this->assertTrue($bar->isEnabled());
    }

    public function testDefaultCollectorsRegistered(): void
    {
        $bar = DebugBar::getInstance();

        $this->assertTrue($bar->hasCollector('time'));
        $this->assertTrue($bar->hasCollector('memory'));
        $this->assertTrue($bar->hasCollector('queries'));
        $this->assertTrue($bar->hasCollector('route'));
        $this->assertTrue($bar->hasCollector('views'));
        $this->assertTrue($bar->hasCollector('logs'));
        $this->assertTrue($bar->hasCollector('request'));
        $this->assertTrue($bar->hasCollector('session'));
        $this->assertTrue($bar->hasCollector('auth'));
        $this->assertTrue($bar->hasCollector('cache'));
        $this->assertTrue($bar->hasCollector('events'));
        $this->assertTrue($bar->hasCollector('config'));
        $this->assertTrue($bar->hasCollector('history'));
    }

    public function testPerformanceMeasures(): void
    {
        $bar = DebugBar::getInstance();
        $bar->startMeasure('test_op', 'Testing Operation');
        usleep(2000); // 2ms
        $bar->stopMeasure('test_op');

        $result = $bar->measure('Closure Measure', function () {
            return 42;
        });

        $this->assertSame(42, $result);

        $data = $bar->collect();
        $this->assertArrayHasKey('time', $data);
        $this->assertGreaterThanOrEqual(2, count($data['time']['measures']));
    }

    public function testLoggingAndMessages(): void
    {
        $bar = DebugBar::getInstance();
        $bar->info('User logged in');
        $bar->warning('Low disk space');
        $bar->error('Database connection failed');
        $bar->debug(['key' => 'value']);

        $data = $bar->collect();
        $this->assertArrayHasKey('logs', $data);
        $this->assertSame(4, $data['logs']['count']);
    }

    public function testFacade(): void
    {
        DebugBarFacade::enable();
        $this->assertTrue(DebugBarFacade::isEnabled());

        DebugBarFacade::info('Message from facade');
        $data = DebugBarFacade::getFacadeRoot()->collect();

        $this->assertSame(1, $data['logs']['count']);
    }
}
