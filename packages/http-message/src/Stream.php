<?php

declare(strict_types=1);

namespace Switch\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    /**
     * @var resource|null
     */
    private $stream;

    private bool $seekable = false;
    private bool $readable = false;
    private bool $writable = false;
    private ?int $size = null;

    /**
     * @param resource $stream Resource stream
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a valid resource.');
        }

        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);

        $this->seekable = $meta['seekable'] ?? false;
        $mode = $meta['mode'] ?? '';

        $this->readable = str_contains($mode, 'r') || str_contains($mode, '+');
        $this->writable = str_contains($mode, 'w') || str_contains($mode, 'a') || str_contains($mode, 'x') || str_contains($mode, 'c') || str_contains($mode, '+');
    }

    public static function create(string $content = ''): self
    {
        $resource = fopen('php://temp', 'r+');
        if ($resource === false) {
            throw new RuntimeException('Failed to create temporary stream resource.');
        }
        if ($content !== '') {
            fwrite($resource, $content);
            fseek($resource, 0);
        }
        return new self($resource);
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (\Throwable) {
            return '';
        }
    }

    public function close(): void
    {
        if ($this->stream !== null) {
            if (is_resource($this->stream)) {
                fclose($this->stream);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        $result = $this->stream;
        $this->stream = null;
        $this->size = null;
        $this->readable = false;
        $this->writable = false;
        $this->seekable = false;
        return $result;
    }

    public function getSize(): ?int
    {
        if ($this->stream === null) {
            return null;
        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
        }

        return $this->size;
    }

    public function tell(): int
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }

        $result = ftell($this->stream);
        if ($result === false) {
            throw new RuntimeException('Failed to determine stream position.');
        }

        return $result;
    }

    public function eof(): bool
    {
        if ($this->stream === null) {
            return true;
        }

        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }

        if (!$this->seekable) {
            throw new RuntimeException('Stream is not seekable.');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Failed to seek on stream.');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }

        if (!$this->writable) {
            throw new RuntimeException('Stream is not writable.');
        }

        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new RuntimeException('Failed to write to stream.');
        }

        $this->size = null;
        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }

        if (!$this->readable) {
            throw new RuntimeException('Stream is not readable.');
        }

        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new RuntimeException('Failed to read from stream.');
        }

        return $result;
    }

    public function getContents(): string
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }

        if (!$this->readable) {
            throw new RuntimeException('Stream is not readable.');
        }

        $result = stream_get_contents($this->stream);
        if ($result === false) {
            throw new RuntimeException('Failed to get stream contents.');
        }

        return $result;
    }

    public function getMetadata(?string $key = null): mixed
    {
        if ($this->stream === null) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);
        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
