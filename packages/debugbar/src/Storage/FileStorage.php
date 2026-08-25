<?php

declare(strict_types=1);

namespace Switch\DebugBar\Storage;

class FileStorage implements StorageInterface
{
    private string $storagePath;

    public function __construct(?string $storagePath = null)
    {
        $this->storagePath = $storagePath
            ? rtrim($storagePath, '/\\')
            : sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'switch_debugbar';

        if (!is_dir($this->storagePath)) {
            @mkdir($this->storagePath, 0777, true);
        }
    }

    public function save(string $id, array $data): void
    {
        $file = $this->getFilePath($id);
        $payload = [
            'id' => $id,
            'time' => microtime(true),
            'method' => $data['request']['request']['method'] ?? 'GET',
            'uri' => $data['request']['request']['uri'] ?? '/',
            'status' => $data['request']['response']['status_code'] ?? 200,
            'duration' => $data['time']['duration_formatted'] ?? '0ms',
            'memory' => $data['memory']['peak_allocated_formatted'] ?? '0MB',
            'data' => $data,
        ];

        @file_put_contents($file, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Garbage collection: randomly prune old files
        if (mt_rand(1, 20) === 1) {
            $this->prune();
        }
    }

    public function get(string $id): ?array
    {
        $file = $this->getFilePath($id);
        if (!file_exists($file)) {
            return null;
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $json = json_decode($content, true);
        return $json['data'] ?? null;
    }

    public function latest(int $limit = 20): array
    {
        $files = glob($this->storagePath . DIRECTORY_SEPARATOR . '*.json');
        if (empty($files)) {
            return [];
        }

        // Sort files by modification time descending
        usort($files, fn(string $a, string $b) => filemtime($b) <=> filemtime($a));

        $list = [];
        foreach (array_slice($files, 0, $limit) as $file) {
            $content = @file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $json = json_decode($content, true);
            if (!is_array($json)) {
                continue;
            }

            $list[] = [
                'id' => $json['id'] ?? basename($file, '.json'),
                'time' => $json['time'] ?? filemtime($file),
                'time_formatted' => date('H:i:s', (int) ($json['time'] ?? filemtime($file))),
                'method' => $json['method'] ?? 'GET',
                'uri' => $json['uri'] ?? '/',
                'status' => $json['status'] ?? 200,
                'duration' => $json['duration'] ?? '0ms',
                'memory' => $json['memory'] ?? '0MB',
            ];
        }

        return $list;
    }

    public function clear(): void
    {
        $files = glob($this->storagePath . DIRECTORY_SEPARATOR . '*.json');
        if (!empty($files)) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    private function prune(int $maxFiles = 40): void
    {
        $files = glob($this->storagePath . DIRECTORY_SEPARATOR . '*.json');
        if (empty($files) || count($files) <= $maxFiles) {
            return;
        }

        usort($files, fn(string $a, string $b) => filemtime($b) <=> filemtime($a));

        $toDelete = array_slice($files, $maxFiles);
        foreach ($toDelete as $f) {
            @unlink($f);
        }
    }

    private function getFilePath(string $id): string
    {
        $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
        return $this->storagePath . DIRECTORY_SEPARATOR . $safeId . '.json';
    }
}
