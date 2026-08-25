<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\DebugBar\DebugBar;
use Switch\DebugBar\Http\Middleware\DebugBarMiddleware;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Stream;

class MiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        DebugBar::setInstance(null);
    }

    public function testMiddlewareInjectsIntoHtmlResponse(): void
    {
        $middleware = new DebugBarMiddleware();
        $request = new ServerRequest('GET', '/');

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $html = '<!DOCTYPE html><html><head><title>App</title></head><body><h1>Hello</h1></body></html>';
                return new Response(200, ['Content-Type' => 'text/html'], Stream::create($html));
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertTrue($response->hasHeader('X-Switch-Debug-Bar'));
        $body = (string) $response->getBody();
        $this->assertStringContainsString('switch-debugbar', $body);
        $this->assertStringContainsString('</body></html>', $body);
    }

    public function testMiddlewareDoesNotInjectIntoJsonResponse(): void
    {
        $middleware = new DebugBarMiddleware();
        $request = new ServerRequest('GET', '/api/users');

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'application/json'], Stream::create('{"status":"ok"}'));
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertTrue($response->hasHeader('X-Switch-Debug-Bar'));
        $body = (string) $response->getBody();
        $this->assertSame('{"status":"ok"}', $body);
    }

    public function testMiddlewareBypassedWhenDisabled(): void
    {
        $bar = DebugBar::getInstance();
        $bar->disable();

        $middleware = new DebugBarMiddleware($bar);
        $request = new ServerRequest('GET', '/');

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/html'], Stream::create('<body>No Bar</body>'));
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertFalse($response->hasHeader('X-Switch-Debug-Bar'));
        $this->assertSame('<body>No Bar</body>', (string) $response->getBody());
    }
}
