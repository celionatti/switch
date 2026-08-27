<?php

declare(strict_types=1);

namespace Switch\Diagram\Console;

use Switch\Console\Command\Command;
use Switch\Diagram\Diagram;
use Switch\Diagram\Exporters\DbmlExporter;
use Switch\Diagram\Exporters\JsonExporter;
use Switch\Diagram\Exporters\MermaidExporter;

class DiagramExportCommand extends Command
{
    protected string $signature = 'diagram:export {--format=mermaid : Export format: mermaid, dbml, or json} {--output= : Optional output file path}';
    protected string $description = 'Export live database schema into Mermaid ER, DBML, or JSON format';
    protected string $category = 'Database';

    public function handle(): int
    {
        $this->title('SWITCH SCHEMA DIAGRAM EXPORTER');

        $format = strtolower((string) ($this->option('format') ?: 'mermaid'));
        $output = $this->option('output');

        $diagram = Diagram::getInstance();
        $schema = $diagram->getSchema();

        $this->info("Discovered " . count($schema) . " database tables.");

        $content = match ($format) {
            'dbml' => DbmlExporter::export($schema),
            'json' => JsonExporter::export($schema),
            default => MermaidExporter::export($schema),
        };

        if ($output) {
            file_put_contents($output, $content);
            $this->success("Schema diagram exported successfully to: {$output}");
        } else {
            $this->line();
            $this->line($content);
            $this->line();
        }

        return 0;
    }
}
