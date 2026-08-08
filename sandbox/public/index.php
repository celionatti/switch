<?php

declare(strict_types=1);

use App\Controller\ApiController;
use App\Controller\HomeController;
use App\Model\Product;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Config\Config;
use Switch\Container\Container;
use Switch\Database\Connection\Connection;
use Switch\Database\ORM\Model;
use Switch\Database\Schema\Blueprint;
use Switch\Database\Schema\SchemaBuilder;
use Switch\Event\EventDispatcher;
use Switch\Event\ListenerProvider;
use Switch\Kernel\App;
use Switch\Kernel\Event\RequestReceivedEvent;
use Switch\Router\Router;
use Switch\View\Engine\ViewEngine;

require_once __DIR__ . '/../../vendor/autoload.php';

// 1. Configuration Subsystem
$config = new Config();
$config->loadFromDirectory(__DIR__ . '/../config');

// 2. Database Subsystem
$db = Connection::sqlite(__DIR__ . '/../storage/sandbox.sqlite');
Model::setConnection($db);

$schema = new SchemaBuilder($db);
if (!$schema->hasTable('products')) {
    $schema->create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->boolean('in_stock')->default(true);
        $table->timestamps();
    });

    Product::create(['name' => 'Switch Framework Mug', 'price' => 14.99, 'in_stock' => true]);
    Product::create(['name' => 'Modular PHP T-Shirt', 'price' => 24.99, 'in_stock' => true]);
    Product::create(['name' => 'PSR Compliance Sticker Pack', 'price' => 4.99, 'in_stock' => true]);
}

// 3. View Subsystem with HTML Tag Compiler Engine
$viewEngine = new ViewEngine(__DIR__ . '/../views', __DIR__ . '/../storage/views');

// 4. Dependency Injection Container Subsystem
$container = new Container();
$container->instance(Config::class, $config);
$container->instance(Connection::class, $db);
$container->instance(ViewEngine::class, $viewEngine);
$container->singleton(HomeController::class);
$container->singleton(ApiController::class);

// 5. Event Subsystem (PSR-14)
$listenerProvider = new ListenerProvider();
$listenerProvider->addListener(RequestReceivedEvent::class, function (RequestReceivedEvent $event) {
    error_log(sprintf('[Event] Received request: %s %s', $event->request->getMethod(), $event->request->getUri()->getPath()));
});
$eventDispatcher = new EventDispatcher($listenerProvider);

// 6. Router Subsystem
$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->group(['prefix' => '/api'], function (Router $router) {
    $router->get('/users/{id}', [ApiController::class, 'getUser'])->where('id', '[0-9]+');
});

// 7. Kernel App Subsystem
$app = new App($container, $eventDispatcher, $router);

// Global Middleware Example
$app->use(new class implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = $handler->handle($request);
        return $response->withHeader('X-Powered-By', 'Switch PHP Framework');
    }
});

// Execute Sandbox App Request
$app->run();
