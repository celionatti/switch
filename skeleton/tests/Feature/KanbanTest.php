<?php

declare(strict_types=1);

namespace Tests\Feature;

use Switch\Router\Facade\Route;
use Switch\Router\Router;
use Tests\TestCase;

class KanbanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $router = new Router();
        Route::setRouter($router);

        require __DIR__ . '/../../routes/web.php';
    }

    public function testKanbanPageRenders(): void
    {
        $response = $this->get('/kanban');

        $response->assertOk()
            ->assertSee('Interactive Drag & Drop & Multi-Table Sort', escape: false)
            ->assertSee('switch-sortable="/api/kanban/reorder-table"', escape: false)
            ->assertSee('switch-sortable-group="kanban"', escape: false)
            ->assertSee('FEAT-1')
            ->assertSee('TSK-101');
    }

    public function testDragDropAliasRouteRenders(): void
    {
        $response = $this->get('/drag-drop');

        $response->assertOk()
            ->assertSee('switch-sortable', escape: false);
    }

    public function testReorderTableEndpoint(): void
    {
        $response = $this->post('/api/kanban/reorder-table', [
            'ids' => ['FEAT-2', 'FEAT-1', 'FEAT-3'],
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'order' => ['FEAT-2', 'FEAT-1', 'FEAT-3'],
            ]);
    }

    public function testMoveCardEndpoint(): void
    {
        $response = $this->post('/api/kanban/move-card', [
            'id' => 'TSK-101',
            'source_group' => 'backlog',
            'target_group' => 'in_progress',
            'order' => ['TSK-103', 'TSK-101', 'TSK-104'],
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'card_id' => 'TSK-101',
                'target_group' => 'in_progress',
            ]);
    }
}
