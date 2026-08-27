<?php

declare(strict_types=1);

namespace Switch\Diagram\Exporters;

use Switch\Diagram\Schema\TableMetadata;

class JsonExporter
{
    /**
     * Export tables and relationships as structured JSON.
     *
     * @param array<string, TableMetadata> $tables
     */
    public static function export(array $tables): string
    {
        $data = [
            'generator' => 'Switch Framework Schema Diagram',
            'version' => '1.0.0',
            'extracted_at' => date('Y-m-d H:i:s'),
            'table_count' => count($tables),
            'tables' => array_map(fn(TableMetadata $table) => $table->toArray(), array_values($tables)),
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
