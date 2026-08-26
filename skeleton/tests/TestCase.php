<?php

declare(strict_types=1);

namespace Tests;

use Switch\Database\Connection\Connection;
use Switch\Database\Migration\MigrationRepository;
use Switch\Database\Migration\MigrationRunner;
use Switch\Database\ORM\Model;
use Switch\Foundation\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Connection $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize in-memory SQLite database for test environment
        $this->db = Connection::sqlite(':memory:');
        Model::setConnection($this->db);

        // Run migrations
        $repository = new MigrationRepository($this->db);
        $runner = new MigrationRunner($this->db, $repository);

        $migrations = [
            '2026_01_01_000000_create_users_table' => require __DIR__ . '/../database/migrations/2026_01_01_000000_create_users_table.php',
            '2026_01_01_000001_create_posts_table' => require __DIR__ . '/../database/migrations/2026_01_01_000001_create_posts_table.php',
            '2026_01_01_000002_create_passwordless_tokens_table' => require __DIR__ . '/../database/migrations/2026_01_01_000002_create_passwordless_tokens_table.php',
        ];

        $runner->run($migrations);

        // Configure View Engine for test rendering
        if (class_exists(\Switch\View\View::class) && class_exists(\Switch\View\Engine\ViewEngine::class)) {
            $viewEngine = new \Switch\View\Engine\ViewEngine(
                viewsPath: __DIR__ . '/../resources/views',
                cachePath: sys_get_temp_dir() . '/switch_test_views'
            );
            \Switch\View\View::setEngine($viewEngine);
        }
    }
}
