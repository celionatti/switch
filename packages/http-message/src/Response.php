<?php

declare(strict_types=1);

namespace Switch\Http;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use MessageTrait;

    private int $statusCode;
    private string $reasonPhrase;

    private const REASON_PHRASES = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    public function __construct(
        int $status = 200,
        array $headers = [],
        ?StreamInterface $body = null,
        string $reasonPhrase = ''
    ) {
        $this->setStatusCode($status, $reasonPhrase);

        if ($body !== null) {
            $this->body = $body;
        }

        foreach ($headers as $name => $value) {
            $lower = strtolower($name);
            $this->headers[$lower] = is_array($value) ? array_map('strval', $value) : [strval($value)];
            $this->headerNames[$lower] = $name;
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->setStatusCode($code, $reasonPhrase);
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    private function setStatusCode(int $code, string $reasonPhrase = ''): void
    {
        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException("Invalid HTTP status code: {$code}");
        }

        $this->statusCode = $code;
        if ($reasonPhrase === '' && isset(self::REASON_PHRASES[$code])) {
            $this->reasonPhrase = self::REASON_PHRASES[$code];
        } else {
            $this->reasonPhrase = $reasonPhrase;
        }
    }
}
