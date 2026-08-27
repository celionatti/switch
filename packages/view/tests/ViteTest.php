<?php

declare(strict_types=1);

namespace Switch\View\Tests;

use PHPUnit\Framework\TestCase;
use Switch\View\Compiler\TemplateCompiler;
use Switch\View\Vite\ViteManifest;

class ViteTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/switch_vite_test_' . uniqid();
        mkdir($this->tmpDir . '/public/build', 0777, true);
    }

    protected function tearDown(): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tmpDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }

        rmdir($this->tmpDir);
    }

    public function testViteDevServerHotFileDetection(): void
    {
        file_put_contents($this->tmpDir . '/public/hot', "http://localhost:5173\n");

        $vite = new ViteManifest($this->tmpDir);
        $html = $vite->render(['resources/css/app.css', 'resources/js/app.js']);

        $this->assertStringContainsString('http://localhost:5173/@vite/client', $html);
        $this->assertStringContainsString('<link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">', $html);
        $this->assertStringContainsString('<script type="module" src="http://localhost:5173/resources/js/app.js"></script>', $html);
    }

    public function testViteProductionManifestResolution(): void
    {
        $manifest = [
            'resources/js/app.js' => [
                'file' => 'assets/app-a1b2c3d4.js',
                'css' => ['assets/app-e5f6g7h8.css'],
            ],
            'resources/css/custom.css' => [
                'file' => 'assets/custom-11223344.css',
            ],
        ];

        file_put_contents($this->tmpDir . '/public/build/manifest.json', json_encode($manifest));

        $vite = new ViteManifest($this->tmpDir);
        $html = $vite->render(['resources/css/custom.css', 'resources/js/app.js']);

        $this->assertStringContainsString('<link rel="stylesheet" href="/build/assets/custom-11223344.css">', $html);
        $this->assertStringContainsString('<link rel="stylesheet" href="/build/assets/app-e5f6g7h8.css">', $html);
        $this->assertStringContainsString('<script type="module" src="/build/assets/app-a1b2c3d4.js"></script>', $html);
    }

    public function testTemplateCompilerCompilesViteDirectives(): void
    {
        $compiler = new TemplateCompiler();

        $bladeCompiled = $compiler->compile("@vite(['resources/css/app.css', 'resources/js/app.js'])");
        $this->assertStringContainsString('ViteManifest', $bladeCompiled);

        $tagCompiled = $compiler->compile('<vite entry="resources/js/app.js" />');
        $this->assertStringContainsString('ViteManifest', $tagCompiled);
    }
}
