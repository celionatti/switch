<?php

declare(strict_types=1);

namespace Switch\DebugBar\Renderer;

class JsonRenderer
{
    /**
     * Render debug data as structured JSON.
     *
     * @param array<string, mixed> $collectedData
     * @param string $requestId
     * @return string
     */
    public function render(array $collectedData, string $requestId): string
    {
        $payload = [
            'id' => $requestId,
            'timestamp' => microtime(true),
            'collectors' => $collectedData,
        ];

        return (string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
