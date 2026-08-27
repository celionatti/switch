<?php

declare(strict_types=1);

namespace Switch\Diagram\Tests;

use PHPUnit\Framework\TestCase;
use Switch\Diagram\Exporters\DbmlExporter;
use Switch\Diagram\Exporters\JsonExporter;
use Switch\Diagram\Exporters\MermaidExporter;
use Switch\Diagram\Schema\ColumnMetadata;
use Switch\Diagram\Schema\RelationMetadata;
use Switch\Diagram\Schema\TableMetadata;

class ExporterTest extends TestCase
{
    /**
     * @return array<string, TableMetadata>
     */
    private function createSampleSchema(): array
    {
        $users = new TableMetadata('users', rowCount: 10);
        $users->addColumn(new ColumnMetadata('id', 'INTEGER', isPrimaryKey: true));
        $users->addColumn(new ColumnMetadata('email', 'VARCHAR', nullable: false, isUnique: true));

        $posts = new TableMetadata('posts', rowCount: 25);
        $posts->addColumn(new ColumnMetadata('id', 'INTEGER', isPrimaryKey: true));
        $posts->addColumn(new ColumnMetadata('user_id', 'INTEGER', isForeignKey: true, foreignTable: 'users', foreignColumn: 'id'));
        $posts->addColumn(new ColumnMetadata('title', 'VARCHAR', nullable: false));

        $posts->addRelation(new RelationMetadata(
            sourceTable: 'posts',
            sourceColumn: 'user_id',
            targetTable: 'users',
            targetColumn: 'id',
            relationType: RelationMetadata::TYPE_BELONGS_TO,
            relationName: 'author'
        ));

        return [
            'users' => $users,
            'posts' => $posts,
        ];
    }

    public function testMermaidExporterProducesValidSyntax(): void
    {
        $tables = $this->createSampleSchema();
        $mermaid = MermaidExporter::export($tables);

        $this->assertStringContainsString('erDiagram', $mermaid);
        $this->assertStringContainsString('posts }o--|| users : "author"', $mermaid);
        $this->assertStringContainsString('users {', $mermaid);
        $this->assertStringContainsString('integer id PK', $mermaid);
        $this->assertStringContainsString('posts {', $mermaid);
        $this->assertStringContainsString('integer user_id FK', $mermaid);
    }

    public function testDbmlExporterProducesValidSyntax(): void
    {
        $tables = $this->createSampleSchema();
        $dbml = DbmlExporter::export($tables);

        $this->assertStringContainsString('Table users {', $dbml);
        $this->assertStringContainsString('id integer [pk', $dbml);
        $this->assertStringContainsString('email varchar [not null', $dbml);
        $this->assertStringContainsString('Table posts {', $dbml);
        $this->assertStringContainsString('Ref: posts.user_id > users.id', $dbml);
    }

    public function testJsonExporterProducesStructuredData(): void
    {
        $tables = $this->createSampleSchema();
        $json = JsonExporter::export($tables);

        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertEquals(2, $data['table_count']);
        $this->assertEquals('Switch Framework Schema Diagram', $data['generator']);
        $this->assertCount(2, $data['tables']);
        $this->assertEquals('users', $data['tables'][0]['name']);
    }
}
