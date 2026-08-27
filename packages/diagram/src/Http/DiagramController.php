<?php

declare(strict_types=1);

namespace Switch\Diagram\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Diagram\Diagram;
use Switch\Diagram\Exporters\JsonExporter;
use Switch\Http\Response;
use Switch\Http\Stream;

class DiagramController
{
    public function __construct(
        private readonly ?Diagram $diagram = null
    ) {
    }

    private function getDiagram(): Diagram
    {
        return $this->diagram ?? Diagram::getInstance();
    }

    /**
     * Render the standalone ER diagram HTML page.
     */
    public function render(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getDiagram()->renderStandalone();
    }

    /**
     * Return schema data as JSON.
     */
    public function data(ServerRequestInterface $request): ResponseInterface
    {
        $schema = $this->getDiagram()->getSchema();
        $json = JsonExporter::export($schema);

        return new Response(200, ['Content-Type' => 'application/json'], Stream::create($json));
    }
}
