<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Controller\Controller;

class KanbanController extends Controller
{
    /**
     * Display the interactive Drag & Drop and Multi-Table Sorting Showcase.
     */
    public function index(): string
    {
        // 1. Single Table Sortable Demo Data
        $tableItems = [
            [
                'id' => 'FEAT-1',
                'title' => 'Vite Asset HMR Integration',
                'priority' => 'Critical',
                'category' => 'View Engine',
                'estimate' => '3 pts',
                'status' => 'Shipped',
            ],
            [
                'id' => 'FEAT-2',
                'title' => 'RFC 6238 Two-Factor TOTP Subsystem',
                'priority' => 'High',
                'category' => 'Security',
                'estimate' => '5 pts',
                'status' => 'Shipped',
            ],
            [
                'id' => 'FEAT-3',
                'title' => 'Live SPA Drag & Drop Reordering',
                'priority' => 'High',
                'category' => 'Live SPA',
                'estimate' => '2 pts',
                'status' => 'Active',
            ],
            [
                'id' => 'FEAT-4',
                'title' => 'Zero-Config Database Seeders Architecture',
                'priority' => 'Medium',
                'category' => 'Database',
                'estimate' => '3 pts',
                'status' => 'Shipped',
            ],
            [
                'id' => 'FEAT-5',
                'title' => 'Domain Actions CLI Scaffolding Generators',
                'priority' => 'Medium',
                'category' => 'CLI Tools',
                'estimate' => '1 pt',
                'status' => 'Shipped',
            ],
        ];

        // 2. Multi-Table / Kanban Board Cross-Transfer Demo Data
        $backlogTasks = [
            [
                'id' => 'TSK-101',
                'title' => 'Distributed Redis lock manager for queued workers',
                'tag' => 'Infrastructure',
                'tag_color' => 'indigo',
                'assignee' => 'Marcus Vance',
            ],
            [
                'id' => 'TSK-102',
                'title' => 'GraphQL schema introspection compiler',
                'tag' => 'API',
                'tag_color' => 'cyan',
                'assignee' => 'Sarah Connor',
            ],
        ];

        $inProgressTasks = [
            [
                'id' => 'TSK-103',
                'title' => 'SSE real-time notification push stream',
                'tag' => 'Live SPA',
                'tag_color' => 'emerald',
                'assignee' => 'Elena Rostova',
            ],
            [
                'id' => 'TSK-104',
                'title' => 'AES-256 encrypted session cookie rotation',
                'tag' => 'Security',
                'tag_color' => 'amber',
                'assignee' => 'David Kim',
            ],
        ];

        $completedTasks = [
            [
                'id' => 'TSK-105',
                'title' => '60 FPS Optimistic DOM morphing engine',
                'tag' => 'Core Engine',
                'tag_color' => 'emerald',
                'assignee' => 'Alex Thorne',
            ],
            [
                'id' => 'TSK-106',
                'title' => 'Zero-dependency PSR-7/PSR-15 HTTP pipeline',
                'tag' => 'Kernel',
                'tag_color' => 'cyan',
                'assignee' => 'Celio Natti',
            ],
        ];

        return $this->view('showcase.kanban', [
            'title' => 'Drag & Drop & Multi-Table Kanban — Switch Framework',
            'version' => '1.0.0',
            'tableItems' => $tableItems,
            'backlogTasks' => $backlogTasks,
            'inProgressTasks' => $inProgressTasks,
            'completedTasks' => $completedTasks,
        ]);
    }

    /**
     * Handle single-table row reordering.
     */
    public function reorderTable(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        if (empty($body)) {
            $raw = (string) $request->getBody();
            $body = json_decode($raw, true) ?: [];
        }

        $ids = (array) ($body['ids'] ?? $body['order'] ?? []);
        $count = count($ids);

        $this->toast("Roadmap order updated ({$count} items repositioned in < 1ms)", 'success');

        return $this->json([
            'success' => true,
            'message' => 'Table reordered successfully',
            'order' => $ids,
        ]);
    }

    /**
     * Handle Kanban / Multi-table cross-column transfer.
     */
    public function moveCard(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        if (empty($body)) {
            $raw = (string) $request->getBody();
            $body = json_decode($raw, true) ?: [];
        }

        $cardId = (string) ($body['id'] ?? 'Task');
        $sourceGroup = (string) ($body['source_group'] ?? 'previous');
        $targetGroup = (string) ($body['target_group'] ?? 'new');

        $labels = [
            'backlog' => 'Sprint Backlog',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];

        $targetName = $labels[$targetGroup] ?? ucfirst($targetGroup);

        $this->toast("Card {$cardId} moved to [{$targetName}] with 0ms UI lag!", 'info');

        return $this->json([
            'success' => true,
            'card_id' => $cardId,
            'target_group' => $targetGroup,
        ]);
    }
}
