<?php

declare(strict_types=1);

namespace Switch\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    private string $method;
    private ?string $requestTarget = null;
    private UriInterface $uri;

    public function __construct(
        string $method = 'GET',
        UriInterface|string $uri = '',
        ?StreamInterface $body = null,
        array $headers = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = is_string($uri) ? new Uri($uri) : $uri;

        if ($body !== null) {
            $this->body = $body;
        }

        foreach ($headers as $name => $value) {
            $lower = strtolower($name);
            $this->headers[$lower] = is_array($value) ? array_map('strval', $value) : [strval($value)];
            $this->headerNames[$lower] = $name;
        }

        if (!$this->hasHeader('Host') && $this->uri->getHost() !== '') {
            $host = $this->uri->getHost();
            if ($this->uri->getPort() !== null) {
                $host .= ':' . $this->uri->getPort();
            }
            $this->headers['host'] = [$host];
            $this->headerNames['host'] = 'Host';
        }
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }

        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if ($this->requestTarget === $requestTarget) {
            return $this;
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $method = strtoupper($method);
        if ($this->method === $method) {
            return $this;
        }

        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($this->uri === $uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !$new->hasHeader('Host')) {
            if ($uri->getHost() !== '') {
                $host = $uri->getHost();
                if ($uri->getPort() !== null) {
                    $host .= ':' . $uri->getPort();
                }
                $new->headers['host'] = [$host];
                $new->headerNames['host'] = 'Host';
            }
        }

        return $new;
    }
}
