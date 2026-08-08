<?php

declare(strict_types=1);

namespace Switch\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    private string $protocolVersion = '1.1';

    /**
     * @var array<string, array<int, string>> Header name (lowercase) => values
     */
    private array $headers = [];

    /**
     * @var array<string, string> Lowercase header name => original case header name
     */
    private array $headerNames = [];

    private ?StreamInterface $body = null;

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($this->protocolVersion === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->headers as $lower => $values) {
            $name = $this->headerNames[$lower] ?? $lower;
            $headers[$name] = $values;
        }
        return $headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $lower = strtolower($name);
        return $this->headers[$lower] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $lower = strtolower($name);
        $values = $this->normalizeHeaderValue($value);

        $new = clone $this;
        $new->headers[$lower] = $values;
        $new->headerNames[$lower] = $name;
        return $new;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $lower = strtolower($name);
        $values = $this->normalizeHeaderValue($value);

        $new = clone $this;
        if (!isset($new->headers[$lower])) {
            $new->headers[$lower] = $values;
            $new->headerNames[$lower] = $name;
        } else {
            $new->headers[$lower] = array_merge($new->headers[$lower], $values);
        }

        return $new;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $lower = strtolower($name);
        if (!isset($this->headers[$lower])) {
            return $this;
        }

        $new = clone $this;
        unset($new->headers[$lower], $new->headerNames[$lower]);
        return $new;
    }

    public function getBody(): StreamInterface
    {
        if ($this->body === null) {
            $this->body = Stream::create('');
        }
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($this->body === $body) {
            return $this;
        }

        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    /**
     * @param string|array<int, string> $value
     * @return array<int, string>
     */
    private function normalizeHeaderValue(mixed $value): array
    {
        if (is_array($value)) {
            return array_map('strval', array_values($value));
        }

        return [strval($value)];
    }
}
