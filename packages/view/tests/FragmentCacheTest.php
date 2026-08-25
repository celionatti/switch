<?php

declare(strict_types=1);

namespace Switch\View\Tests;

use PHPUnit\Framework\TestCase;
use Switch\View\Cache\FragmentCache;
use Switch\View\Compiler\TemplateCompiler;

class FragmentCacheTest extends TestCase
{
    private TemplateCompiler $compiler;

    protected function setUp(): void
    {
        $this->compiler = new TemplateCompiler();
        FragmentCache::flush();
    }

    public function testCompilerParsesCacheDirectives(): void
    {
        $template = "@cache('sidebar', 300, ['nav'])\n<nav>Menu</nav>\n@endcache";
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::start('sidebar', 300, ['nav'])", $compiled);
        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::end()", $compiled);
    }

    public function testCompilerParsesCacheHtmlTags(): void
    {
        $template = "<cache key=\"user-card\" ttl=\"600\" tags=\"users,profiles\">\n<div>User Card</div>\n</cache>";
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::start('user-card', 600, 'users,profiles')", $compiled);
        $this->assertStringContainsString("Switch\View\Cache\FragmentCache::end()", $compiled);
    }

    public function testFragmentCacheHitAndMissLifecycle(): void
    {
        $renderCount = 0;

        $renderFragment = function () use (&$renderCount) {
            ob_start();
            if (!FragmentCache::start('widget-stats', 300)) {
                $renderCount++;
                echo "<span>Dynamic Stat Value: " . $renderCount . "</span>";
                echo FragmentCache::end();
            }
            return ob_get_clean();
        };

        // First render - Cache Miss
        $out1 = $renderFragment();
        $this->assertEquals(1, $renderCount);
        $this->assertStringContainsString('Dynamic Stat Value: 1', $out1);

        // Second render - Cache Hit
        $out2 = $renderFragment();
        $this->assertEquals(1, $renderCount); // Count stayed 1 because inner block was skipped!
        $this->assertStringContainsString('Dynamic Stat Value: 1', $out2);

        // Flush cache
        FragmentCache::flush();

        // Third render - Cache Miss again
        $out3 = $renderFragment();
        $this->assertEquals(2, $renderCount);
        $this->assertStringContainsString('Dynamic Stat Value: 2', $out3);
    }

    public function testFragmentCacheTaggedInvalidation(): void
    {
        $counter = 0;

        $render = function () use (&$counter) {
            ob_start();
            if (!FragmentCache::start('tagged-widget', 300, ['dashboard', 'stats'])) {
                $counter++;
                echo "Counter: " . $counter;
                echo FragmentCache::end();
            }
            return ob_get_clean();
        };

        $this->assertEquals("Counter: 1", $render());
        $this->assertEquals("Counter: 1", $render());

        // Invalidate by unrelated tag -> should still be cached
        FragmentCache::flush(['unrelated']);
        $this->assertEquals("Counter: 1", $render());

        // Invalidate by matching tag -> should bust cache
        FragmentCache::flush(['dashboard']);
        $this->assertEquals("Counter: 2", $render());
    }
}
