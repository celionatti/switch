<?php

declare(strict_types=1);

namespace Switch\Diagram\Exporters;

use Switch\Diagram\Schema\RelationMetadata;
use Switch\Diagram\Schema\TableMetadata;

class DbmlExporter
{
    /**
     * Export tables and relationships as DBML (Database Markup Language) syntax.
     *
     * @param array<string, TableMetadata> $tables
     */
    public static function export(array $tables): string
    {
        $lines = ["// Switch Framework Generated DBML Schema", ""];

        // 1. Tables
        foreach ($tables as $table) {
            $lines[] = "Table {$table->name} {";
            foreach ($table->columns as $col) {
                $type = strtolower($col->type ?: 'varchar');
                $settings = [];

                if ($col->isPrimaryKey) {
                    $settings[] = 'pk';
                }
                if (!$col->nullable) {
                    $settings[] = 'not null';
                }
                if ($col->isUnique) {
                    $settings[] = 'unique';
                }
                if ($col->defaultValue !== null) {
                    $settings[] = "default: `{$col->defaultValue}`";
                }
                if ($col->comment) {
                    $settings[] = "note: '{$col->comment}'";
                }

                $settingStr = !empty($settings) ? ' [' . implode(', ', $settings) . ']' : '';
                $lines[] = "  {$col->name} {$type}{$settingStr}";
            }
            $lines[] = "}";
            $lines[] = "";
        }

        // 2. References / Relationships
        $rendered = [];
        foreach ($tables as $table) {
            foreach ($table->relations as $rel) {
                $source = "{$rel->sourceTable}.{$rel->sourceColumn}";
                $target = "{$rel->targetTable}.{$rel->targetColumn}";

                $symbol = match ($rel->relationType) {
                    RelationMetadata::TYPE_HAS_MANY => '<',
                    RelationMetadata::TYPE_HAS_ONE => '-',
                    RelationMetadata::TYPE_BELONGS_TO_MANY => '<>',
                    default => '>',
                };

                $refKey = "{$source}_{$symbol}_{$target}";
                if (!isset($rendered[$refKey])) {
                    $lines[] = "Ref: {$source} {$symbol} {$target}";
                    $rendered[$refKey] = true;
                }
            }
        }

        return implode("\n", $lines);
    }
}
