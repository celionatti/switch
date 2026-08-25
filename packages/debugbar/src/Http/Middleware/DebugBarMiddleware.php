<?php

declare(strict_types=1);

namespace Switch\DebugBar\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\DebugBar\Collectors\RequestCollector;
use Switch\DebugBar\Collectors\TimeCollector;
use Switch\DebugBar\DebugBar;
use Switch\DebugBar\Http\DebugBarController;

class DebugBarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ?DebugBar $debugbar = null
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $bar = $this->debugbar ?? DebugBar::getInstance();

        if (!$bar->isEnabled()) {
            return $handler->handle($request);
        }

        // Handle internal debugbar endpoint
        $path = $request->getUri()->getPath();
        if ($path === '/_debugbar/data' || $path === $bar->getDataUrl()) {
            $controller = new DebugBarController();
            return $controller->getData($request);
        }

        // Feed request to collector
        if ($bar->hasCollector('request')) {
            $reqCollector = $bar->getCollector('request');
            if ($reqCollector instanceof RequestCollector) {
                $reqCollector->setRequest($request);
            }
        }

        $bar->startMeasure('application', 'Application Execution');

        try {
            $response = $handler->handle($request);
        } finally {
            $bar->stopMeasure('application');
        }

        if ($bar->hasCollector('request')) {
            $reqCollector = $bar->getCollector('request');
            if ($reqCollector instanceof RequestCollector) {
                $reqCollector->setResponse($response);
            }
        }

        if ($bar->hasCollector('time')) {
            $timeCollector = $bar->getCollector('time');
            if ($timeCollector instanceof TimeCollector) {
                $timeCollector->setRequestEndTime(microtime(true));
            }
        }

        return $bar->inject($response);
    }
}
