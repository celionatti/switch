<?php

declare(strict_types=1);

namespace Switch\Diagram\Schema;

class ColumnMetadata
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $nullable = false,
        public readonly bool $isPrimaryKey = false,
        public readonly bool $isForeignKey = false,
        public readonly ?string $foreignTable = null,
        public readonly ?string $foreignColumn = null,
        public readonly mixed $defaultValue = null,
        public readonly bool $isIndexed = false,
        public readonly bool $isUnique = false,
        public readonly ?string $comment = null
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'nullable' => $this->nullable,
            'is_primary_key' => $this->isPrimaryKey,
            'is_foreign_key' => $this->isForeignKey,
            'foreign_table' => $this->foreignTable,
            'foreign_column' => $this->foreignColumn,
            'default' => $this->defaultValue,
            'is_indexed' => $this->isIndexed,
            'is_unique' => $this->isUnique,
            'comment' => $this->comment,
        ];
    }
}
