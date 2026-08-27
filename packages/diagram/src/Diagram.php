<?php

declare(strict_types=1);

namespace Switch\Diagram;

use Psr\Http\Message\ResponseInterface;
use Switch\Database\Connection\Connection;
use Switch\Diagram\Renderer\DiagramRenderer;
use Switch\Diagram\Schema\SchemaExtractor;
use Switch\Diagram\Schema\TableMetadata;
use Switch\Http\Response;
use Switch\Http\Stream;

class Diagram
{
    private static ?self $instance = null;

    private bool $enabled = true;
    private string $routePath = '/_diagram';
    private array $ignoreTables = ['sqlite_sequence', 'migrations'];
    private array $modelPaths = [];
    private ?Connection $connection = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function enable(): self
    {
        $this->enabled = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setRoutePath(string $path): self
    {
        $this->routePath = '/' . ltrim($path, '/');
        return $this;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function setIgnoreTables(array $tables): self
    {
        $this->ignoreTables = $tables;
        return $this;
    }

    public function setModelPaths(array $paths): self
    {
        $this->modelPaths = $paths;
        return $this;
    }

    public function setConnection(?Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return array<string, TableMetadata>
     */
    public function getSchema(): array
    {
        $extractor = new SchemaExtractor(
            connection: $this->connection,
            ignoreTables: $this->ignoreTables,
            modelPaths: $this->modelPaths
        );

        return $extractor->extract();
    }

    /**
     * Render standalone full page view.
     */
    public function renderStandalone(): ResponseInterface
    {
        $renderer = new DiagramRenderer($this->getSchema());
        $html = $renderer->renderStandalone();

        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], Stream::create($html));
    }

    /**
     * Inject floating trigger into an HTML response.
     */
    public function inject(ResponseInterface $response): ResponseInterface
    {
        if (!$this->enabled) {
            return $response;
        }

        $contentType = $response->getHeaderLine('Content-Type');
        if (!str_contains(strtolower($contentType), 'text/html')) {
            return $response;
        }

        $body = (string) $response->getBody();
        if ($body === '' || !str_contains($body, '</body>')) {
            return $response;
        }

        $renderer = new DiagramRenderer($this->getSchema());
        $drawerHtml = $renderer->renderDrawer();

        $injectedBody = str_replace('</body>', $drawerHtml . "\n</body>", $body);

        return $response
            ->withBody(Stream::create($injectedBody))
            ->withoutHeader('Content-Length')
            ->withHeader('Content-Length', (string) strlen($injectedBody));
    }
}
