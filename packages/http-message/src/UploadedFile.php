<?php

declare(strict_types=1);

namespace Switch\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    private ?StreamInterface $stream = null;
    private ?string $file = null;
    private bool $moved = false;

    public function __construct(
        StreamInterface|string $streamOrFile,
        private readonly ?int $size = null,
        private readonly int $error = UPLOAD_ERR_OK,
        private readonly ?string $clientFilename = null,
        private readonly ?string $clientMediaType = null
    ) {
        if (is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } else {
            $this->stream = $streamOrFile;
        }
    }

    public function getStream(): StreamInterface
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after file has been moved.');
        }

        if ($this->stream === null) {
            if ($this->file === null || !file_exists($this->file)) {
                throw new RuntimeException('Uploaded file resource is not accessible.');
            }
            $resource = fopen($this->file, 'r');
            if ($resource === false) {
                throw new RuntimeException("Unable to open file: {$this->file}");
            }
            $this->stream = new Stream($resource);
        }

        return $this->stream;
    }

    public function moveTo(string $targetPath): void
    {
        if ($this->moved) {
            throw new RuntimeException('Uploaded file has already been moved.');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot move file due to upload error: ' . $this->error);
        }

        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            throw new InvalidArgumentException("Target directory is not writable: {$targetDir}");
        }

        if ($this->file !== null) {
            $this->moved = PHP_SAPI === 'cli' ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);
        } else {
            $dest = fopen($targetPath, 'w');
            if ($dest === false) {
                throw new RuntimeException("Unable to open target file for writing: {$targetPath}");
            }
            $stream = $this->getStream();
            $stream->rewind();
            while (!$stream->eof()) {
                fwrite($dest, $stream->read(4096));
            }
            fclose($dest);
            $this->moved = true;
        }

        if (!$this->moved) {
            throw new RuntimeException("Failed to move uploaded file to {$targetPath}");
        }
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
