<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Switch\DebugBar\Dumper\HtmlDumper;

class DumperTest extends TestCase
{
    public function testDumpScalars(): void
    {
        $this->assertStringContainsString('null', HtmlDumper::dump(null));
        $this->assertStringContainsString('true', HtmlDumper::dump(true));
        $this->assertStringContainsString('false', HtmlDumper::dump(false));
        $this->assertStringContainsString('12345', HtmlDumper::dump(12345));
        $this->assertStringContainsString('Hello World', HtmlDumper::dump('Hello World'));
    }

    public function testDumpArray(): void
    {
        $arr = ['name' => 'Switch', 'version' => 1.0, 'active' => true];
        $html = HtmlDumper::dump($arr);

        $this->assertStringContainsString('array:3', $html);
        $this->assertStringContainsString('name', $html);
        $this->assertStringContainsString('Switch', $html);
    }

    public function testDumpObject(): void
    {
        $obj = new class {
            public string $name = 'Testing';
            private int $secret = 999;
        };

        $html = HtmlDumper::dump($obj);
        $this->assertStringContainsString('Testing', $html);
    }

    public function testDumpCircularReferenceSafety(): void
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $a->b = $b;
        $b->a = $a;

        $html = HtmlDumper::dump($a);
        $this->assertStringContainsString('#circular', $html);
    }
}
