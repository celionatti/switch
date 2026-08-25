<?php

declare(strict_types=1);

namespace Switch\DebugBar\Storage;

class MemoryStorage implements StorageInterface
{
    private static array $store = [];

    public function save(string $id, array $data): void
    {
        self::$store[$id] = [
            'data' => $data,
            'time' => microtime(true),
            'method' => $data['request']['request']['method'] ?? 'GET',
            'uri' => $data['request']['request']['uri'] ?? '/',
            'status' => $data['request']['response']['status_code'] ?? 200,
            'duration' => $data['time']['duration_formatted'] ?? '0ms',
            'memory' => $data['memory']['peak_allocated_formatted'] ?? '0MB',
        ];

        // Keep last 30 requests in memory
        if (count(self::$store) > 30) {
            array_shift(self::$store);
        }
    }

    public function get(string $id): ?array
    {
        return self::$store[$id]['data'] ?? null;
    }

    public function latest(int $limit = 20): array
    {
        $list = [];
        foreach (array_reverse(self::$store, true) as $id => $item) {
            $list[] = [
                'id' => $id,
                'time' => $item['time'],
                'time_formatted' => date('H:i:s', (int) $item['time']),
                'method' => $item['method'],
                'uri' => $item['uri'],
                'status' => $item['status'],
                'duration' => $item['duration'],
                'memory' => $item['memory'],
            ];

            if (count($list) >= $limit) {
                break;
            }
        }

        return $list;
    }

    public function clear(): void
    {
        self::$store = [];
    }
}
