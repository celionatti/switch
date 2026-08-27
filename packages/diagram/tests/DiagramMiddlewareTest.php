<?php

declare(strict_types=1);

namespace Switch\Diagram\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Diagram\Diagram;
use Switch\Diagram\Http\Middleware\DiagramMiddleware;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Stream;
use Switch\Http\Uri;

class DiagramMiddlewareTest extends TestCase
{
    private function createHandler(string $body = '<html><body><h1>App Page</h1></body></html>', string $contentType = 'text/html'): RequestHandlerInterface
    {
        return new class($body, $contentType) implements RequestHandlerInterface {
            public function __construct(private string $body, private string $contentType) {}

            public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                return new Response(200, ['Content-Type' => $this->contentType], Stream::create($this->body));
            }
        };
    }

    public function testMiddlewareInterceptsStandaloneRoute(): void
    {
        $diagram = new Diagram();
        $diagram->enable();
        $middleware = new DiagramMiddleware($diagram);

        $request = new ServerRequest('GET', new Uri('http://localhost:8000/_diagram'));
        $response = $middleware->process($request, $this->createHandler());

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertStringContainsString('Switch Diagram', $body);
        $this->assertStringContainsString('sd-workspace', $body);
    }

    public function testMiddlewareInterceptsDataEndpoint(): void
    {
        $diagram = new Diagram();
        $diagram->enable();
        $middleware = new DiagramMiddleware($diagram);

        $request = new ServerRequest('GET', new Uri('http://localhost:8000/_diagram/data'));
        $response = $middleware->process($request, $this->createHandler());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $json = (string) $response->getBody();
        $this->assertStringContainsString('Switch Framework Schema Diagram', $json);
    }

    public function testMiddlewareInjectsFloatingTriggerIntoHtmlResponses(): void
    {
        $diagram = new Diagram();
        $diagram->enable();
        $middleware = new DiagramMiddleware($diagram);

        $request = new ServerRequest('GET', new Uri('http://localhost:8000/dashboard'));
        $response = $middleware->process($request, $this->createHandler());

        $body = (string) $response->getBody();
        $this->assertStringContainsString('switch-diagram-trigger', $body);
        $this->assertStringContainsString('switch-diagram-container', $body);
        $this->assertStringContainsString('</body>', $body);
    }

    public function testMiddlewareDoesNotInjectIntoJsonResponses(): void
    {
        $diagram = new Diagram();
        $diagram->enable();
        $middleware = new DiagramMiddleware($diagram);

        $request = new ServerRequest('GET', new Uri('http://localhost:8000/api/users'));
        $response = $middleware->process($request, $this->createHandler('{"users": []}', 'application/json'));

        $body = (string) $response->getBody();
        $this->assertStringNotContainsString('switch-diagram-trigger', $body);
        $this->assertEquals('{"users": []}', $body);
    }

    public function testMiddlewareBypassedWhenDisabled(): void
    {
        $diagram = new Diagram();
        $diagram->disable();
        $middleware = new DiagramMiddleware($diagram);

        $request = new ServerRequest('GET', new Uri('http://localhost:8000/_diagram'));
        $response = $middleware->process($request, $this->createHandler('<h1>App Page</h1>'));

        $body = (string) $response->getBody();
        $this->assertEquals('<h1>App Page</h1>', $body);
    }
}
