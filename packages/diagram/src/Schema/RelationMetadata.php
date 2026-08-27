<?php

declare(strict_types=1);

namespace Switch\Diagram\Schema;

class RelationMetadata
{
    public const TYPE_BELONGS_TO = 'belongsTo';
    public const TYPE_HAS_MANY = 'hasMany';
    public const TYPE_HAS_ONE = 'hasOne';
    public const TYPE_BELONGS_TO_MANY = 'belongsToMany';

    public function __construct(
        public readonly string $sourceTable,
        public readonly string $sourceColumn,
        public readonly string $targetTable,
        public readonly string $targetColumn,
        public readonly string $relationType = self::TYPE_BELONGS_TO,
        public readonly ?string $relationName = null,
        public readonly ?string $pivotTable = null,
        public readonly bool $isOrmVirtual = false
    ) {
    }

    /**
     * Get the visual cardinality representation (e.g. "1:N", "1:1", "N:M").
     */
    public function getCardinalityLabel(): string
    {
        return match ($this->relationType) {
            self::TYPE_HAS_MANY => '1 : N',
            self::TYPE_HAS_ONE => '1 : 1',
            self::TYPE_BELONGS_TO_MANY => 'N : M',
            default => 'N : 1',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'source_table' => $this->sourceTable,
            'source_column' => $this->sourceColumn,
            'target_table' => $this->targetTable,
            'target_column' => $this->targetColumn,
            'relation_type' => $this->relationType,
            'relation_name' => $this->relationName,
            'cardinality' => $this->getCardinalityLabel(),
            'pivot_table' => $this->pivotTable,
            'is_orm_virtual' => $this->isOrmVirtual,
        ];
    }
}
