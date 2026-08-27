<?php

declare(strict_types=1);

namespace Switch\Diagram\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Diagram\Diagram;
use Switch\Diagram\Http\DiagramController;

class DiagramMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ?Diagram $diagram = null
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $diagram = $this->diagram ?? Diagram::getInstance();

        if (!$diagram->isEnabled()) {
            return $handler->handle($request);
        }

        $path = $request->getUri()->getPath();
        $baseRoute = $diagram->getRoutePath();

        // 1. Intercept standalone route: /_diagram
        if ($path === $baseRoute || $path === $baseRoute . '/') {
            $controller = new DiagramController($diagram);
            return $controller->render($request);
        }

        // 2. Intercept data API endpoint: /_diagram/data
        if ($path === $baseRoute . '/data') {
            $controller = new DiagramController($diagram);
            return $controller->data($request);
        }

        // Process host application request
        $response = $handler->handle($request);

        // Inject floating trigger & drawer into HTML responses
        return $diagram->inject($response);
    }
}
