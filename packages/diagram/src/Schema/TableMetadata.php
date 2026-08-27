<?php

declare(strict_types=1);

namespace Switch\Diagram\Schema;

class TableMetadata
{
    /**
     * @param array<string, ColumnMetadata> $columns
     * @param array<int, RelationMetadata> $relations
     * @param array<int, array<string, mixed>> $sampleRows
     */
    public function __construct(
        public readonly string $name,
        public array $columns = [],
        public array $relations = [],
        public int $rowCount = 0,
        public ?string $modelClass = null,
        public bool $hasSoftDeletes = false,
        public array $sampleRows = [],
        public ?string $comment = null
    ) {
    }

    public function addColumn(ColumnMetadata $column): self
    {
        $this->columns[$column->name] = $column;
        return $this;
    }

    public function addRelation(RelationMetadata $relation): self
    {
        $this->relations[] = $relation;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'model_class' => $this->modelClass,
            'row_count' => $this->rowCount,
            'has_soft_deletes' => $this->hasSoftDeletes,
            'comment' => $this->comment,
            'columns' => array_map(fn(ColumnMetadata $col) => $col->toArray(), array_values($this->columns)),
            'relations' => array_map(fn(RelationMetadata $rel) => $rel->toArray(), $this->relations),
            'sample_rows' => $this->sampleRows,
        ];
    }
}
