<?php

declare(strict_types=1);

namespace Switch\DebugBar\Storage;

interface StorageInterface
{
    /**
     * Save a request debug payload by unique ID.
     */
    public function save(string $id, array $data): void;

    /**
     * Retrieve a request debug payload by unique ID.
     */
    public function get(string $id): ?array;

    /**
     * Retrieve metadata for the latest requests.
     *
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 20): array;

    /**
     * Clear old stored requests.
     */
    public function clear(): void;
}
