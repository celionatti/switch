<?php

declare(strict_types=1);

namespace Tests\Feature;

use Switch\View\Cache\FragmentCache;
use Switch\View\Compiler\TemplateCompiler;
use Switch\View\Engine\ViewEngine;
use Tests\TestCase;

class ViewDirectivesTest extends TestCase
{
    private TemplateCompiler $compiler;
    private ViewEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compiler = new TemplateCompiler();
        $this->engine = new ViewEngine(
            viewsPath: __DIR__ . '/../../resources/views',
            cachePath: __DIR__ . '/../../storage/views'
        );
        FragmentCache::flush();
    }

    public function testViewCompilerCompilesDirectivesAndComponents(): void
    {
        $template = <<<'SWITCH'
@cache('hero_block', 600, ['home'])
    <x-card title="Welcome to Switch" hoverable="true">
        <p>Ultra-fast full-stack PHP framework.</p>
        <x-button variant="primary">Get Started</x-button>
    </x-card>
@endcache
SWITCH;

        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::start('hero_block', 600, ['home'])", $compiled);
        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::end()", $compiled);
        $this->assertStringContainsString("ComponentRegistry::render('card'", $compiled);
        $this->assertStringContainsString("ComponentRegistry::render('button'", $compiled);
    }

    public function testSecurityDirectivesCompile(): void
    {
        $template = "@csrf\n@honeypot\n@nonce";
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('SecurityHelper::csrfField()', $compiled);
        $this->assertStringContainsString('SecurityHelper::honeypot', $compiled);
        $this->assertStringContainsString('SecurityHelper::getCspNonce()', $compiled);
    }

    public function testViewFragmentCachingLifecycle(): void
    {
        $counter = 0;
        $renderFunc = function () use (&$counter) {
            ob_start();
            if (!FragmentCache::start('stats-counter', 300)) {
                $counter++;
                echo "Counter Value: {$counter}";
                echo FragmentCache::end();
            }
            return ob_get_clean();
        };

        // 1st render - Miss
        $out1 = $renderFunc();
        $this->assertEquals(1, $counter);
        $this->assertEquals('Counter Value: 1', $out1);

        // 2nd render - Hit (counter shouldn't increment)
        $out2 = $renderFunc();
        $this->assertEquals(1, $counter);
        $this->assertEquals('Counter Value: 1', $out2);

        // Invalidate
        FragmentCache::flush();

        // 3rd render - Miss (counter increments)
        $out3 = $renderFunc();
        $this->assertEquals(2, $counter);
        $this->assertEquals('Counter Value: 2', $out3);
    }
}
