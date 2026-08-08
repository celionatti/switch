<?php

declare(strict_types=1);

namespace Switch\Http;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $uriObject = is_string($uri) ? new Uri($uri) : $uri;
        return new ServerRequest($method, $uriObject, [], null, '1.1', $serverParams);
    }
}
