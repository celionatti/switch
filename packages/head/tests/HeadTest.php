<?php

declare(strict_types=1);

namespace Switch\Head\Tests;

use PHPUnit\Framework\TestCase;
use Switch\Head\Head;
use Switch\Head\HeadManager;

require_once __DIR__ . '/../src/helpers.php';

class HeadTest extends TestCase
{
    protected function setUp(): void
    {
        Head::reset();
    }

    public function testTitleAndTemplate(): void
    {
        Head::setTitle('Dashboard');
        Head::setTitleTemplate('%s - Switch Framework');

        $this->assertEquals('Dashboard - Switch Framework', Head::getTitle());
        $rendered = Head::render();
        $this->assertStringContainsString('<title>Dashboard - Switch Framework</title>', $rendered);
    }

    public function testMetaAndOpenGraphAndTwitter(): void
    {
        Head::description('A fast PHP framework')
            ->keywords(['php', 'framework', 'switch'])
            ->og('title', 'Switch Framework')
            ->og('image', 'https://example.com/og.jpg')
            ->twitter('card', 'summary_large_image');

        $rendered = Head::render();

        $this->assertStringContainsString('name="description" content="A fast PHP framework"', $rendered);
        $this->assertStringContainsString('name="keywords" content="php, framework, switch"', $rendered);
        $this->assertStringContainsString('property="og:title" content="Switch Framework"', $rendered);
        $this->assertStringContainsString('property="og:image" content="https://example.com/og.jpg"', $rendered);
        $this->assertStringContainsString('name="twitter:card" content="summary_large_image"', $rendered);
    }

    public function testCanonicalAndFaviconAndPreload(): void
    {
        Head::setCanonical('https://example.com/canonical-page')
            ->addFavicon('/favicon.ico')
            ->addPreload('/font.woff2', 'font', 'font/woff2');

        $rendered = Head::render();

        $this->assertStringContainsString('<link rel="canonical" href="https://example.com/canonical-page">', $rendered);
        $this->assertStringContainsString('<link rel="icon" href="/favicon.ico" type="image/x-icon">', $rendered);
        $this->assertStringContainsString('<link rel="preload" href="/font.woff2" as="font" type="font/woff2">', $rendered);
    }

    public function testJsonLdStructuredData(): void
    {
        Head::addJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Switch',
        ]);

        $rendered = Head::render();

        $this->assertStringContainsString('<script type="application/ld+json">', $rendered);
        $this->assertStringContainsString('"@type": "Organization"', $rendered);
    }

    public function testGlobalHeadHelperFunction(): void
    {
        head()->setTitle('Helper Test');
        $this->assertEquals('Helper Test', head()->getTitle());
    }
}
