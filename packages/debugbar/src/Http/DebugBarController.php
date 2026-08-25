<?php

declare(strict_types=1);

namespace Switch\DebugBar\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\DebugBar\DebugBar;
use Switch\Http\Response;

class DebugBarController
{
    /**
     * Retrieve stored debug data for a request ID via AJAX.
     */
    public function getData(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $id = $params['id'] ?? null;

        if ($id === null || empty($id)) {
            return $this->createJsonResponse(['error' => 'Missing request id parameter'], 400);
        }

        $debugbar = DebugBar::getInstance();
        $storage = $debugbar->getStorage();

        if ($storage === null) {
            return $this->createJsonResponse(['error' => 'No storage configured'], 500);
        }

        $data = $storage->get((string) $id);
        if ($data === null) {
            return $this->createJsonResponse(['error' => 'Debug data not found for id: ' . $id], 404);
        }

        return $this->createJsonResponse($data);
    }

    private function createJsonResponse(array $data, int $status = 200): ResponseInterface
    {
        if (class_exists(\Switch\Http\Response::class)) {
            return \Switch\Http\Response::json($data, $status);
        }

        $json = (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $stream = class_exists(\Switch\Http\Stream::class)
            ? \Switch\Http\Stream::create($json)
            : null;

        if (class_exists(\Switch\Http\Response::class) && $stream !== null) {
            return new \Switch\Http\Response($status, ['Content-Type' => 'application/json'], $stream);
        }

        throw new \RuntimeException('No PSR-7 Response implementation found to send DebugBar data.');
    }
}
