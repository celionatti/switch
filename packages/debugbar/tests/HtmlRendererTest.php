<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Switch\DebugBar\DebugBar;

class HtmlRendererTest extends TestCase
{
    protected function setUp(): void
    {
        DebugBar::setInstance(null);
    }

    public function testRenderProducesCompleteMarkupAndScripts(): void
    {
        $bar = DebugBar::getInstance();
        $bar->info('Testing render');
        $html = $bar->render();

        $this->assertStringContainsString('id="switch-debugbar"', $html);
        $this->assertStringContainsString('id="sdb-main-bar"', $html);
        $this->assertStringContainsString('id="sdb-floating-pill"', $html);
        $this->assertStringContainsString('id="sdb-main-drawer"', $html);
        $this->assertStringContainsString('__switchDebugBarInit', $html);
        $this->assertStringContainsString('Overview', $html);
        $this->assertStringContainsString('Timeline', $html);
        $this->assertStringContainsString('Queries', $html);
    }

    public function testRenderJson(): void
    {
        $bar = DebugBar::getInstance();
        $json = $bar->renderJson();

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('id', $decoded);
        $this->assertArrayHasKey('collectors', $decoded);
    }
}
