<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Switch\DebugBar\Collectors\SecurityCollector;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Uri;

class SecurityCollectorTest extends TestCase
{
    public function testSecurityCollectorInspectsRuntimeAndProducesScore(): void
    {
        $collector = new SecurityCollector();

        $request = new ServerRequest('GET', new Uri('https://example.com/checkout'));
        $response = new Response(200, ['Content-Type' => 'text/html']);

        $collector->setRequestResponse($request, $response);
        $data = $collector->collect();

        $this->assertArrayHasKey('score', $data);
        $this->assertArrayHasKey('grade', $data);
        $this->assertArrayHasKey('counts', $data);
        $this->assertArrayHasKey('results', $data);
        $this->assertNotEmpty($data['results']);
    }

    public function testSecurityCollectorBadgeReflectsAlertCount(): void
    {
        $collector = new SecurityCollector();
        $badge = $collector->getBadge();
        $this->assertNotNull($badge);
    }
}
