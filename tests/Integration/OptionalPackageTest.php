<?php

declare(strict_types=1);

namespace Switch\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Stream;
use Switch\Kernel\App;

class OptionalPackageTest extends TestCase
{
    public function testKernelWorksWithoutContainerRouterOrEvents(): void
    {
        // Construct App kernel with NULL container, NULL eventDispatcher, NULL router
        $app = new App(container: null, eventDispatcher: null, router: null);

        // Add custom middleware handler
        $app->use(function (ServerRequestInterface $request) {
            return new Response(200, ['Content-Type' => 'text/plain'], Stream::create('Kernel Minimal OK'));
        });

        $request = new ServerRequest('GET', 'http://localhost/standalone');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Kernel Minimal OK', (string) $response->getBody());
    }

    public function testKernelWorksWithOnlyRouterWithoutContainerOrEvents(): void
    {
        $router = new \Switch\Router\Router();
        $router->get('/minimal-route', fn() => 'Minimal Route Response');

        // Construct App with ONLY router
        $app = new App(container: null, eventDispatcher: null, router: $router);

        $request = new ServerRequest('GET', 'http://localhost/minimal-route');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Minimal Route Response', (string) $response->getBody());
    }
}
