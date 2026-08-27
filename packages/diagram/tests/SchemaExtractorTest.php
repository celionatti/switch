<?php

declare(strict_types=1);

namespace Switch\Diagram\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Switch\Database\Connection\Connection;
use Switch\Database\Connection\ConnectionConfig;
use Switch\Diagram\Schema\ColumnMetadata;
use Switch\Diagram\Schema\RelationMetadata;
use Switch\Diagram\Schema\SchemaExtractor;
use Switch\Diagram\Schema\TableMetadata;

class SchemaExtractorTest extends TestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        $config = new ConnectionConfig(
            driver: 'sqlite',
            database: ':memory:'
        );
        $this->connection = new Connection($config);

        $pdo = $this->connection->getPdo();
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                created_at DATETIME
            );

            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                slug TEXT NOT NULL,
                body TEXT,
                status TEXT DEFAULT 'draft',
                created_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                comment TEXT NOT NULL,
                FOREIGN KEY (post_id) REFERENCES posts(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
        ");

        $pdo->exec("INSERT INTO users (name, email) VALUES ('Alice', 'alice@example.com'), ('Bob', 'bob@example.com')");
        $pdo->exec("INSERT INTO posts (user_id, title, slug, body) VALUES (1, 'Hello World', 'hello-world', 'Post body content')");
    }

    public function testExtractsAllTablesAndColumns(): void
    {
        $extractor = new SchemaExtractor(connection: $this->connection);
        $tables = $extractor->extract();

        $this->assertArrayHasKey('users', $tables);
        $this->assertArrayHasKey('posts', $tables);
        $this->assertArrayHasKey('comments', $tables);

        $users = $tables['users'];
        $this->assertInstanceOf(TableMetadata::class, $users);
        $this->assertEquals('users', $users->name);
        $this->assertEquals(2, $users->rowCount);
        $this->assertCount(2, $users->sampleRows);

        $this->assertArrayHasKey('id', $users->columns);
        $this->assertArrayHasKey('email', $users->columns);

        $idCol = $users->columns['id'];
        $this->assertInstanceOf(ColumnMetadata::class, $idCol);
        $this->assertTrue($idCol->isPrimaryKey);

        $emailCol = $users->columns['email'];
        $this->assertFalse($emailCol->nullable);
    }

    public function testExtractsForeignKeysAndRelations(): void
    {
        $extractor = new SchemaExtractor(connection: $this->connection);
        $tables = $extractor->extract();

        $posts = $tables['posts'];
        $this->assertEquals(1, $posts->rowCount);
        $this->assertArrayHasKey('user_id', $posts->columns);

        $userFk = $posts->columns['user_id'];
        $this->assertTrue($userFk->isForeignKey);
        $this->assertEquals('users', $userFk->foreignTable);
        $this->assertEquals('id', $userFk->foreignColumn);

        $this->assertNotEmpty($posts->relations);
        $rel = $posts->relations[0];
        $this->assertInstanceOf(RelationMetadata::class, $rel);
        $this->assertEquals('posts', $rel->sourceTable);
        $this->assertEquals('user_id', $rel->sourceColumn);
        $this->assertEquals('users', $rel->targetTable);
        $this->assertEquals('id', $rel->targetColumn);
    }

    public function testIgnoredTablesFilter(): void
    {
        $extractor = new SchemaExtractor(
            connection: $this->connection,
            ignoreTables: ['comments', 'sqlite_sequence']
        );
        $tables = $extractor->extract();

        $this->assertArrayHasKey('users', $tables);
        $this->assertArrayHasKey('posts', $tables);
        $this->assertArrayNotHasKey('comments', $tables);
    }
}
