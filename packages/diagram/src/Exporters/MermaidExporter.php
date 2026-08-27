<?php

declare(strict_types=1);

namespace Switch\Diagram\Exporters;

use Switch\Diagram\Schema\RelationMetadata;
use Switch\Diagram\Schema\TableMetadata;

class MermaidExporter
{
    /**
     * Export tables and relationships as Mermaid.js erDiagram syntax.
     *
     * @param array<string, TableMetadata> $tables
     */
    public static function export(array $tables): string
    {
        $lines = ['erDiagram'];

        // 1. Relationships
        $renderedRelations = [];
        foreach ($tables as $table) {
            foreach ($table->relations as $rel) {
                $source = $rel->sourceTable;
                $target = $rel->targetTable;

                // Determine Mermaid connector based on relation type
                $connector = match ($rel->relationType) {
                    RelationMetadata::TYPE_HAS_MANY => '||--o{',
                    RelationMetadata::TYPE_HAS_ONE => '||--||',
                    RelationMetadata::TYPE_BELONGS_TO_MANY => '}o--o{',
                    default => '}o--||', // belongsTo
                };

                $label = $rel->relationName ?: ($rel->sourceColumn . '_to_' . $rel->targetColumn);
                $relKey = "{$source}_{$target}_{$label}";

                if (!isset($renderedRelations[$relKey])) {
                    $lines[] = "    {$source} {$connector} {$target} : \"{$label}\"";
                    $renderedRelations[$relKey] = true;
                }
            }
        }

        $lines[] = '';

        // 2. Table Definitions
        foreach ($tables as $table) {
            $lines[] = "    {$table->name} {";
            foreach ($table->columns as $col) {
                $type = strtolower($col->type ?: 'text');
                $type = preg_replace('/[^a-zA-Z0-9_]/', '_', $type) ?: 'text';
                $name = $col->name;
                $keyType = $col->isPrimaryKey ? 'PK' : ($col->isForeignKey ? 'FK' : '');
                $comment = $col->comment ? "\"{$col->comment}\"" : '';

                $lines[] = sprintf('        %s %s %s %s', $type, $name, $keyType, $comment);
            }
            $lines[] = "    }";
        }

        return implode("\n", $lines);
    }
}
