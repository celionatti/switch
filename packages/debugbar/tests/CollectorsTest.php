<?php

declare(strict_types=1);

namespace Switch\DebugBar\Tests;

use PHPUnit\Framework\TestCase;
use Switch\DebugBar\Collectors\AuthCollector;
use Switch\DebugBar\Collectors\CacheCollector;
use Switch\DebugBar\Collectors\ConfigCollector;
use Switch\DebugBar\Collectors\EventCollector;
use Switch\DebugBar\Collectors\LogCollector;
use Switch\DebugBar\Collectors\MemoryCollector;
use Switch\DebugBar\Collectors\QueryCollector;
use Switch\DebugBar\Collectors\RequestCollector;
use Switch\DebugBar\Collectors\RouteCollector;
use Switch\DebugBar\Collectors\SessionCollector;
use Switch\DebugBar\Collectors\TimeCollector;
use Switch\DebugBar\Collectors\ViewCollector;
use Switch\Http\Response;
use Switch\Http\ServerRequest;

class CollectorsTest extends TestCase
{
    public function testTimeCollector(): void
    {
        $collector = new TimeCollector(microtime(true) - 0.05);
        $collector->startMeasure('m1', 'Measure 1');
        $collector->stopMeasure('m1');

        $data = $collector->collect();
        $this->assertArrayHasKey('duration_formatted', $data);
        $this->assertCount(1, $data['measures']);
        $this->assertNotNull($collector->getBadge());
    }

    public function testMemoryCollector(): void
    {
        $collector = new MemoryCollector();
        $data = $collector->collect();

        $this->assertArrayHasKey('peak_allocated_formatted', $data);
        $this->assertArrayHasKey('memory_limit', $data);
        $this->assertNotNull($collector->getBadge());
    }

    public function testQueryCollectorAndDuplicateDetection(): void
    {
        $collector = new QueryCollector();
        $collector->addQuery('SELECT * FROM users WHERE id = ?', [1], 5.2, 'mysql', 'UserController.php', 42);
        $collector->addQuery('SELECT * FROM users WHERE id = ?', [1], 4.8, 'mysql', 'UserController.php', 45); // Duplicate!
        $collector->addQuery('SELECT * FROM posts WHERE user_id = ?', [1], 60.0, 'mysql', 'PostController.php', 20); // Slow!

        $data = $collector->collect();
        $this->assertSame(3, $data['count']);
        $this->assertSame(1, $data['duplicate_count']);
        $this->assertSame(1, $data['slow_count']);
        $this->assertSame('danger', $collector->getBadgeColor());
    }

    public function testRouteCollector(): void
    {
        $collector = new RouteCollector();
        $collector->setRouteData(
            uri: '/users/{id}',
            method: 'GET',
            action: 'App\\Controllers\\UserController@show',
            middleware: ['auth', 'throttle:60'],
            parameters: ['id' => '42'],
            name: 'users.show'
        );

        $data = $collector->collect();
        $this->assertSame('users.show', $data['name']);
        $this->assertSame('GET', $data['method']);
        $this->assertSame('UserController@show', $collector->getBadge());
    }

    public function testViewCollector(): void
    {
        $collector = new ViewCollector();
        $collector->addView('home.index', '/views/home/index.switch.php', 2.5, ['user' => 'Alex']);

        $data = $collector->collect();
        $this->assertSame(1, $data['count']);
        $this->assertSame(2.5, $data['total_render_time_ms']);
    }

    public function testLogCollector(): void
    {
        $collector = new LogCollector();
        $collector->info('Info log');
        $collector->error('Error occurred');

        $data = $collector->collect();
        $this->assertSame(2, $data['count']);
        $this->assertSame('danger', $collector->getBadgeColor());
    }

    public function testRequestCollector(): void
    {
        $collector = new RequestCollector();
        $request = new ServerRequest('POST', '/api/users', ['Authorization' => 'Bearer secret-token']);
        $response = new Response(201);

        $collector->setRequest($request);
        $collector->setResponse($response);

        $data = $collector->collect();
        $this->assertSame('POST', $data['request']['method']);
        $this->assertSame(201, $data['response']['status_code']);
        $this->assertStringContainsString('MASKED', $data['request']['headers']['Authorization']);
    }

    public function testSessionCollector(): void
    {
        $collector = new SessionCollector();
        $collector->setSessionData(['_token' => 'abc', 'user_id' => 10], 'sess_123');

        $data = $collector->collect();
        $this->assertSame('sess_123', $data['id']);
        $this->assertSame(2, $data['count']);
    }

    public function testAuthCollector(): void
    {
        $collector = new AuthCollector();
        $this->assertSame('Guest', $collector->getBadge());

        $user = new class {
            public int $id = 99;
            public string $email = 'celio@switch.dev';
            public string $name = 'Celio';
        };

        $collector->setUser($user, 'web');
        $data = $collector->collect();

        $this->assertTrue($data['authenticated']);
        $this->assertSame('Celio', $collector->getBadge());
    }

    public function testCacheCollector(): void
    {
        $collector = new CacheCollector();
        $collector->logHit('user:1', ['id' => 1]);
        $collector->logMiss('user:2');
        $collector->logWrite('user:2', ['id' => 2], 3600);

        $data = $collector->collect();
        $this->assertSame(1, $data['hits']);
        $this->assertSame(1, $data['misses']);
        $this->assertSame(1, $data['writes']);
        $this->assertSame(50.0, $data['hit_ratio']);
    }

    public function testEventCollector(): void
    {
        $collector = new EventCollector();
        $collector->logEvent('App\\Events\\UserRegistered', ['SendWelcomeEmail', 'CreateDefaultTeam'], 1.4);

        $data = $collector->collect();
        $this->assertSame(1, $data['count']);
        $this->assertSame(2, $data['events'][0]['listener_count']);
    }

    public function testConfigCollector(): void
    {
        $collector = new ConfigCollector();
        $data = $collector->collect();

        $this->assertArrayHasKey('php_version', $data);
        $this->assertArrayHasKey('loaded_extensions', $data);
        $this->assertArrayHasKey('environment', $data);
    }
}
