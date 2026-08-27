<?php

declare(strict_types=1);

namespace Switch\Diagram\Schema;

use PDO;
use ReflectionClass;
use ReflectionMethod;
use Switch\Database\Connection\Connection;
use Switch\Database\ORM\Model;
use Switch\Database\ORM\Relation\BelongsTo;
use Switch\Database\ORM\Relation\BelongsToMany;
use Switch\Database\ORM\Relation\HasMany;
use Switch\Database\ORM\Relation\HasOne;
use Switch\Database\ORM\Relation\Relation;

class SchemaExtractor
{
    /**
     * @var array<string, TableMetadata>
     */
    private array $tables = [];

    /**
     * @param array<string> $ignoreTables Tables to exclude from schema extraction
     * @param array<string> $modelPaths Directories to scan for ORM models
     */
    public function __construct(
        private ?Connection $connection = null,
        private array $ignoreTables = ['sqlite_sequence', 'migrations'],
        private array $modelPaths = []
    ) {
    }

    /**
     * Extract the complete database and ORM schema graph.
     *
     * @return array<string, TableMetadata>
     */
    public function extract(): array
    {
        $this->tables = [];
        $pdo = $this->getPdo();

        if ($pdo === null) {
            return $this->tables;
        }

        $driver = $this->getDriver();
        $tableNames = $this->getTableNames($pdo, $driver);

        foreach ($tableNames as $tableName) {
            if (in_array($tableName, $this->ignoreTables, true)) {
                continue;
            }

            $table = new TableMetadata(name: $tableName);
            $this->extractColumns($pdo, $driver, $table);
            $this->extractForeignKeys($pdo, $driver, $table);
            $this->extractRowCountAndSamples($pdo, $table);

            $this->tables[$tableName] = $table;
        }

        // Merge ORM Model definitions and virtual relations
        $this->extractModelRelations();

        return $this->tables;
    }

    private function getPdo(): ?PDO
    {
        if ($this->connection !== null) {
            return $this->connection->getPdo();
        }

        if (class_exists(Model::class)) {
            $conn = Model::getConnection();
            if ($conn instanceof Connection) {
                return $conn->getPdo();
            }
        }

        return null;
    }

    private function getDriver(): string
    {
        if ($this->connection !== null) {
            if (method_exists($this->connection, 'getDriverName')) {
                return $this->connection->getDriverName();
            }
            if (method_exists($this->connection, 'getConfig')) {
                return $this->connection->getConfig()->driver ?? 'sqlite';
            }
        }

        if (class_exists(Model::class)) {
            $conn = Model::getConnection();
            if ($conn instanceof Connection) {
                if (method_exists($conn, 'getDriverName')) {
                    return $conn->getDriverName();
                }
                if (method_exists($conn, 'getConfig')) {
                    return $conn->getConfig()->driver ?? 'sqlite';
                }
            }
        }

        $pdo = $this->getPdo();
        if ($pdo instanceof PDO) {
            try {
                return (string) $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            } catch (\Throwable) {
                // Fallback
            }
        }

        return 'sqlite';
    }

    /**
     * @return array<string>
     */
    private function getTableNames(PDO $pdo, string $driver): array
    {
        $names = [];

        try {
            if ($driver === 'sqlite') {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name ASC");
                if ($stmt) {
                    $names = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
                }
            } elseif ($driver === 'mysql') {
                $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                if ($stmt) {
                    $names = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
                }
            } elseif ($driver === 'pgsql') {
                $stmt = $pdo->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' ORDER BY tablename ASC");
                if ($stmt) {
                    $names = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
                }
            }
        } catch (\Throwable) {
            // Fallback empty
        }

        return array_map('strval', $names);
    }

    private function extractColumns(PDO $pdo, string $driver, TableMetadata $table): void
    {
        try {
            if ($driver === 'sqlite') {
                $stmt = $pdo->query("PRAGMA table_info(\"{$table->name}\")");
                if ($stmt) {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    foreach ($rows as $row) {
                        $colName = (string) ($row['name'] ?? '');
                        $type = strtoupper((string) ($row['type'] ?? 'TEXT'));
                        $notNull = (int) ($row['notnull'] ?? 0) === 1;
                        $pk = (int) ($row['pk'] ?? 0) >= 1;
                        $default = $row['dflt_value'] ?? null;

                        $col = new ColumnMetadata(
                            name: $colName,
                            type: $type ?: 'TEXT',
                            nullable: !$notNull,
                            isPrimaryKey: $pk,
                            defaultValue: $default
                        );
                        $table->addColumn($col);
                    }
                }
            } elseif ($driver === 'mysql') {
                $stmt = $pdo->prepare("
                    SELECT column_name, data_type, column_type, is_nullable, column_key, column_default, extra, column_comment
                    FROM information_schema.columns
                    WHERE table_schema = DATABASE() AND table_name = ?
                    ORDER BY ordinal_position ASC
                ");
                $stmt->execute([$table->name]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $row) {
                    $colName = (string) ($row['column_name'] ?? '');
                    $type = strtoupper((string) ($row['column_type'] ?? ($row['data_type'] ?? 'VARCHAR')));
                    $isNullable = strtoupper((string) ($row['is_nullable'] ?? '')) === 'YES';
                    $isPk = strtoupper((string) ($row['column_key'] ?? '')) === 'PRI';
                    $isUnique = strtoupper((string) ($row['column_key'] ?? '')) === 'UNI';
                    $isIndexed = !empty($row['column_key']);

                    $col = new ColumnMetadata(
                        name: $colName,
                        type: $type,
                        nullable: $isNullable,
                        isPrimaryKey: $isPk,
                        defaultValue: $row['column_default'] ?? null,
                        isIndexed: $isIndexed,
                        isUnique: $isUnique,
                        comment: $row['column_comment'] ?? null
                    );
                    $table->addColumn($col);
                }
            } elseif ($driver === 'pgsql') {
                $stmt = $pdo->prepare("
                    SELECT column_name, data_type, udt_name, is_nullable, column_default
                    FROM information_schema.columns
                    WHERE table_schema = 'public' AND table_name = ?
                    ORDER BY ordinal_position ASC
                ");
                $stmt->execute([$table->name]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $row) {
                    $colName = (string) ($row['column_name'] ?? '');
                    $type = strtoupper((string) ($row['udt_name'] ?? ($row['data_type'] ?? 'VARCHAR')));
                    $isNullable = strtoupper((string) ($row['is_nullable'] ?? '')) === 'YES';

                    $col = new ColumnMetadata(
                        name: $colName,
                        type: $type,
                        nullable: $isNullable,
                        isPrimaryKey: str_contains((string) ($row['column_default'] ?? ''), 'nextval') || $colName === 'id',
                        defaultValue: $row['column_default'] ?? null
                    );
                    $table->addColumn($col);
                }
            }
        } catch (\Throwable) {
            // Ignored
        }
    }

    private function extractForeignKeys(PDO $pdo, string $driver, TableMetadata $table): void
    {
        try {
            if ($driver === 'sqlite') {
                $stmt = $pdo->query("PRAGMA foreign_key_list(\"{$table->name}\")");
                if ($stmt) {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    foreach ($rows as $row) {
                        $fromCol = (string) ($row['from'] ?? '');
                        $toTable = (string) ($row['table'] ?? '');
                        $toCol = (string) ($row['to'] ?? 'id');

                        if (isset($table->columns[$fromCol])) {
                            $old = $table->columns[$fromCol];
                            $table->columns[$fromCol] = new ColumnMetadata(
                                name: $old->name,
                                type: $old->type,
                                nullable: $old->nullable,
                                isPrimaryKey: $old->isPrimaryKey,
                                isForeignKey: true,
                                foreignTable: $toTable,
                                foreignColumn: $toCol,
                                defaultValue: $old->defaultValue,
                                isIndexed: $old->isIndexed,
                                isUnique: $old->isUnique,
                                comment: $old->comment
                            );
                        }

                        $relation = new RelationMetadata(
                            sourceTable: $table->name,
                            sourceColumn: $fromCol,
                            targetTable: $toTable,
                            targetColumn: $toCol,
                            relationType: RelationMetadata::TYPE_BELONGS_TO,
                            relationName: $fromCol . '_fk'
                        );
                        $table->addRelation($relation);
                    }
                }
            } elseif ($driver === 'mysql') {
                $stmt = $pdo->prepare("
                    SELECT column_name, referenced_table_name, referenced_column_name, constraint_name
                    FROM information_schema.key_column_usage
                    WHERE table_schema = DATABASE() AND table_name = ? AND referenced_table_name IS NOT NULL
                ");
                $stmt->execute([$table->name]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                foreach ($rows as $row) {
                    $fromCol = (string) ($row['column_name'] ?? '');
                    $toTable = (string) ($row['referenced_table_name'] ?? '');
                    $toCol = (string) ($row['referenced_column_name'] ?? 'id');

                    if (isset($table->columns[$fromCol])) {
                        $old = $table->columns[$fromCol];
                        $table->columns[$fromCol] = new ColumnMetadata(
                            name: $old->name,
                            type: $old->type,
                            nullable: $old->nullable,
                            isPrimaryKey: $old->isPrimaryKey,
                            isForeignKey: true,
                            foreignTable: $toTable,
                            foreignColumn: $toCol,
                            defaultValue: $old->defaultValue,
                            isIndexed: $old->isIndexed,
                            isUnique: $old->isUnique,
                            comment: $old->comment
                        );
                    }

                    $relation = new RelationMetadata(
                        sourceTable: $table->name,
                        sourceColumn: $fromCol,
                        targetTable: $toTable,
                        targetColumn: $toCol,
                        relationType: RelationMetadata::TYPE_BELONGS_TO,
                        relationName: $row['constraint_name'] ?? ($fromCol . '_fk')
                    );
                    $table->addRelation($relation);
                }
            }
        } catch (\Throwable) {
            // Ignored
        }
    }

    private function extractRowCountAndSamples(PDO $pdo, TableMetadata $table): void
    {
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM \"{$table->name}\"");
            if ($countStmt) {
                $table->rowCount = (int) $countStmt->fetchColumn();
            }

            $sampleStmt = $pdo->query("SELECT * FROM \"{$table->name}\" LIMIT 5");
            if ($sampleStmt) {
                $table->sampleRows = $sampleStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
        } catch (\Throwable) {
            // Fallback
        }
    }

    private function extractModelRelations(): void
    {
        $searchDirs = !empty($this->modelPaths) ? $this->modelPaths : [
            getcwd() . '/app/Models',
            dirname(__DIR__, 4) . '/skeleton/app/Models',
        ];

        $modelClasses = [];
        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.php') ?: [];
                foreach ($files as $file) {
                    require_once $file;
                    $className = 'App\\Models\\' . basename($file, '.php');
                    if (class_exists($className) && is_subclass_of($className, Model::class)) {
                        $modelClasses[] = $className;
                    }
                }
            }
        }

        foreach ($modelClasses as $className) {
            try {
                $ref = new ReflectionClass($className);
                if ($ref->isAbstract()) {
                    continue;
                }

                /** @var Model $instance */
                $instance = $ref->newInstanceWithoutConstructor();
                $tableName = $instance->getTable();

                if (!isset($this->tables[$tableName])) {
                    $this->tables[$tableName] = new TableMetadata(name: $tableName);
                }

                $table = $this->tables[$tableName];
                $table->modelClass = $className;
                $table->hasSoftDeletes = in_array(\Switch\Database\ORM\SoftDeletes::class, $ref->getTraitNames(), true);

                // Inspect relation methods
                foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($method->getNumberOfParameters() > 0 || $method->isStatic()) {
                        continue;
                    }

                    $methodName = $method->getName();
                    if (in_array($methodName, ['flow', 'save', 'delete', 'toArray', 'jsonSerialize', 'getConnection', 'getTable', 'getKey', 'getKeyName'], true)) {
                        continue;
                    }

                    try {
                        $result = $method->invoke($instance);
                        if ($result instanceof Relation) {
                            $this->mapOrmRelationToMetadata($table, $methodName, $result);
                        }
                    } catch (\Throwable) {
                        // Skip methods that require runtime arguments or fail
                    }
                }
            } catch (\Throwable) {
                // Skip uninstantiable models
            }
        }
    }

    private function mapOrmRelationToMetadata(TableMetadata $sourceTable, string $relationName, Relation $relation): void
    {
        $relatedModel = $relation->getRelated();
        $targetTable = $relatedModel->getTable();

        $relationType = match (true) {
            $relation instanceof BelongsTo => RelationMetadata::TYPE_BELONGS_TO,
            $relation instanceof HasMany => RelationMetadata::TYPE_HAS_MANY,
            $relation instanceof HasOne => RelationMetadata::TYPE_HAS_ONE,
            $relation instanceof BelongsToMany => RelationMetadata::TYPE_BELONGS_TO_MANY,
            default => RelationMetadata::TYPE_BELONGS_TO,
        };

        $foreignKey = $relation->getForeignKey();
        $localKey = $relation->getLocalKey();

        // Check if relation already exists from physical FK
        foreach ($sourceTable->relations as $existing) {
            if ($existing->sourceTable === $sourceTable->name && $existing->targetTable === $targetTable && $existing->sourceColumn === $foreignKey) {
                return;
            }
        }

        $sourceTable->addRelation(new RelationMetadata(
            sourceTable: $sourceTable->name,
            sourceColumn: $foreignKey,
            targetTable: $targetTable,
            targetColumn: $localKey,
            relationType: $relationType,
            relationName: $relationName,
            isOrmVirtual: true
        ));
    }
}
