<?php

declare(strict_types=1);

namespace App\Controllers;

use Switch\View\View;
use Switch\Live\LiveResponse;

class HomeController
{
    private static int $count = 0;

    public function index(): string
    {
        return View::render('home', [
            'title' => 'Switch Framework — The High-Velocity PHP Framework',
            'framework' => 'Switch',
            'version' => '1.0.0',
            'phpVersion' => PHP_VERSION,
            'count' => self::$count,
            'features' => [
                [
                    'icon' => '⚡',
                    'tag' => 'Modular Engine',
                    'title' => 'Decoupled PSR Core',
                    'desc' => 'Individual standalone packages for Container, Router, Events, HTTP, and Config with zero overhead.'
                ],
                [
                    'icon' => '🎨',
                    'tag' => 'Native Templates',
                    'title' => 'Switch View Engine',
                    'desc' => 'HTML-first directives, layouts, sections, components, slots, and zero-disk-stat production caching.'
                ],
                [
                    'icon' => '🚀',
                    'tag' => 'Zero-JS Reactivity',
                    'title' => 'Switch Live SPA',
                    'desc' => 'Instant SPA transitions, DOM morphing, hover prefetching, infinite scroll, debounced search, and toasts.'
                ],
                [
                    'icon' => '🗄️',
                    'tag' => 'Multi-Driver ORM',
                    'title' => 'Unified Database',
                    'desc' => 'Fluid query builder, Active Record models, migrations runner, and support for MySQL, PostgreSQL, and SQLite.'
                ],
                [
                    'icon' => '🛡️',
                    'tag' => 'Developer DX',
                    'title' => 'Error Handler & Debugger',
                    'desc' => 'Beautiful interactive stack traces, automated JSON error negotiation, and production-safe exception masking.'
                ],
                [
                    'icon' => '💻',
                    'tag' => 'CLI Suite',
                    'title' => 'Console & Tinker REPL',
                    'desc' => 'Rich CLI commands, code generators, migrations, cache clearing, and an interactive PHP Tinker shell.'
                ]
            ],
            'quickstart' => [
                ['step' => '01', 'command' => 'php switch make:controller UserController', 'label' => 'Generate Controllers & Resources'],
                ['step' => '02', 'command' => 'php switch migrate', 'label' => 'Run Driver-Agnostic Migrations'],
                ['step' => '03', 'command' => 'php switch tinker', 'label' => 'Interactive Database REPL Shell']
            ]
        ]);
    }

    public function incrementCounter(): string
    {
        $current = isset($_POST['count']) ? (int) $_POST['count'] + 1 : 1;
        LiveResponse::toast("Counter increased to {$current}!", 'success');
        return View::render('partials.counter-demo', ['count' => $current]);
    }

    public function decrementCounter(): string
    {
        $current = isset($_POST['count']) ? (int) $_POST['count'] - 1 : 0;
        LiveResponse::toast("Counter decreased to {$current}.", 'info');
        return View::render('partials.counter-demo', ['count' => $current]);
    }
}
