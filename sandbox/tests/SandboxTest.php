<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controller\ApiController;
use App\Controller\HomeController;
use App\Model\Product;
use PHPUnit\Framework\TestCase;
use Switch\Config\Config;
use Switch\Container\Container;
use Switch\Database\Connection\Connection;
use Switch\Database\ORM\Model;
use Switch\Database\Schema\Blueprint;
use Switch\Database\Schema\SchemaBuilder;
use Switch\Event\EventDispatcher;
use Switch\Event\ListenerProvider;
use Switch\Http\ServerRequest;
use Switch\Kernel\App;
use Switch\Router\Router;
use Switch\View\Engine\ViewEngine;

class SandboxTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        $db = Connection::sqlite(':memory:');
        Model::setConnection($db);

        $schema = new SchemaBuilder($db);
        $schema->create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
        });

        Product::create(['name' => 'Switch Test Mug', 'price' => 14.99, 'in_stock' => true]);

        $config = new Config(['app' => ['name' => 'Switch Test App']]);

        $tempViews = sys_get_temp_dir() . '/sandbox_test_views_' . uniqid();
        mkdir($tempViews . '/layouts', 0777, true);
        file_put_contents($tempViews . '/layouts/app.php', '<html><body><yield name="content" /></body></html>');
        file_put_contents(
            $tempViews . '/home.php',
            '<extends name="layouts.app" /><section name="content"><h1>Welcome to {{ $appName }}</h1><ul><foreach items="$products" as="$p"><li>{{ $p.name }}</li></foreach></ul></section>'
        );

        $viewEngine = new ViewEngine($tempViews);

        $container = new Container();
        $container->instance(Config::class, $config);
        $container->instance(Connection::class, $db);
        $container->instance(ViewEngine::class, $viewEngine);
        $container->singleton(HomeController::class);
        $container->singleton(ApiController::class);

        $listenerProvider = new ListenerProvider();
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $router = new Router();
        $router->get('/', [HomeController::class, 'index']);
        $router->get('/api/users/{id}', [ApiController::class, 'getUser'])->where('id', '[0-9]+');

        $this->app = new App($container, $eventDispatcher, $router);
    }

    public function testSandboxHomePageResponse(): void
    {
        $request = new ServerRequest('GET', 'http://localhost/');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('Welcome to Switch Test App', (string) $response->getBody());
        $this->assertStringContainsString('Switch Test Mug', (string) $response->getBody());
    }

    public function testSandboxApiEndpointResponse(): void
    {
        $request = new ServerRequest('GET', 'http://localhost/api/users/99');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $json = json_decode((string) $response->getBody(), true);
        $this->assertEquals('success', $json['status']);
        $this->assertEquals(99, $json['user']['id']);
        $this->assertEquals('Jane Doe', $json['user']['name']);
    }
}
