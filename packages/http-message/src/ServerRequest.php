<?php

declare(strict_types=1);

namespace Switch\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    private array $serverParams;
    private array $cookieParams = [];
    private array $queryParams = [];
    private array $uploadedFiles = [];
    private null|array|object $parsedBody = null;
    private array $attributes = [];

    public function __construct(
        string $method = 'GET',
        UriInterface|string $uri = '',
        array $headers = [],
        ?StreamInterface $body = null,
        string $version = '1.1',
        array $serverParams = []
    ) {
        parent::__construct($method, $uri, $body, $headers);
        $this->protocolVersion = $version;
        $this->serverParams = $serverParams;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $headers = function_exists('getallheaders') ? (getallheaders() ?: []) : [];

        $uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://'
            . ($_SERVER['HTTP_HOST'] ?? 'localhost')
            . ($_SERVER['REQUEST_URI'] ?? '/');

        $body = Stream::create(file_get_contents('php://input') ?: '');

        $request = new self($method, $uri, $headers, $body, '1.1', $_SERVER);
        $request = $request->withCookieParams($_COOKIE)
            ->withQueryParams($_GET);

        if ($method === 'POST' && !empty($_POST)) {
            $request = $request->withParsedBody($_POST);
        }

        return $request;
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function file(string $name): ?\Psr\Http\Message\UploadedFileInterface
    {
        return $this->uploadedFiles[$name] ?? null;
    }

    public function hasFile(string $name): bool
    {
        $file = $this->file($name);
        return $file instanceof \Psr\Http\Message\UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    public function getParsedBody(): null|array|object
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!isset($this->attributes[$name])) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}
