<?php

declare(strict_types=1);

namespace Switch\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Config\Config;
use Switch\Container\Container;
use Switch\Event\EventDispatcher;
use Switch\Event\ListenerProvider;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Stream;
use Switch\Kernel\App;
use Switch\Kernel\Event\RequestReceivedEvent;
use Switch\Kernel\Event\ResponseSendingEvent;
use Switch\Router\Router;

class SampleController
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $environment = $this->config->get('app.env', 'unknown');
        return new Response(200, [], Stream::create("Env: {$environment}"));
    }
}

class FrameworkIntegrationTest extends TestCase
{
    public function testFullPackageStackIntegration(): void
    {
        // 1. Config
        $config = new Config(['app' => ['env' => 'testing']]);

        // 2. Container
        $container = new Container();
        $container->instance(Config::class, $config);
        $container->singleton(SampleController::class);

        // 3. Events
        $receivedDispatched = false;
        $sendingDispatched = false;

        $listenerProvider = new ListenerProvider();
        $listenerProvider->addListener(RequestReceivedEvent::class, function () use (&$receivedDispatched) {
            $receivedDispatched = true;
        });
        $listenerProvider->addListener(ResponseSendingEvent::class, function () use (&$sendingDispatched) {
            $sendingDispatched = true;
        });

        $eventDispatcher = new EventDispatcher($listenerProvider);

        // 4. Router
        $router = new Router();
        $router->get('/env', [SampleController::class, 'handleRequest']);

        // 5. Kernel App
        $app = new App($container, $eventDispatcher, $router);

        $app->use(new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                $res = $handler->handle($request);
                return $res->withHeader('X-Integration-Test', 'Passed');
            }
        });

        // 6. HTTP Request
        $request = new ServerRequest('GET', 'http://localhost/env');
        $response = $app->handle($request);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Env: testing', (string) $response->getBody());
        $this->assertEquals('Passed', $response->getHeaderLine('X-Integration-Test'));
        $this->assertTrue($receivedDispatched, 'RequestReceivedEvent was not dispatched');
        $this->assertTrue($sendingDispatched, 'ResponseSendingEvent was not dispatched');
    }
}
