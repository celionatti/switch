# Switch Framework Workspace

> Modern, modular, zero-dependency PHP framework ecosystem built on PSR standards (PHP 8.2+).

The **Switch Framework** is designed around independent, lightweight Composer packages that can be used together as a complete full-stack framework or used individually in any PHP application.

---

## 📦 Framework Packages

| Package | GitHub Repository | Description | PSR Standard |
|---------|-------------------|-------------|--------------|
| **`switch/container`** | [celionatti/switch-container](https://github.com/celionatti/switch-container) | Fast DI Container with autowiring, singletons, and service providers | **PSR-11** |
| **`switch/http-message`** | [celionatti/switch-http-message](https://github.com/celionatti/switch-http-message) | HTTP Request, Response, ServerRequest, Uri, and Stream implementation | **PSR-7 & PSR-17** |
| **`switch/router`** | [celionatti/switch-router](https://github.com/celionatti/switch-router) | High-performance HTTP Router with dynamic regex parameters & middleware | - |
| **`switch/events`** | [celionatti/switch-events](https://github.com/celionatti/switch-events) | Lightweight Event Dispatcher & Listener Provider | **PSR-14** |
| **`switch/config`** | [celionatti/switch-config](https://github.com/celionatti/switch-config) | Configuration loader supporting dot-notation and environment overrides | - |
| **`switch/kernel`** | [celionatti/switch-kernel](https://github.com/celionatti/switch-kernel) | PSR-15 Middleware Application Kernel with auto-wiring | **PSR-15** |
| **`switch/view`** | [celionatti/switch-view](https://github.com/celionatti/switch-view) | Expressive View Engine with HTML tag syntax (`<if>`, `<foreach>`) & dot-notation | - |
| **`switch/database`** | [celionatti/switch-database](https://github.com/celionatti/switch-database) | Active Record ORM, QueryBuilder, Migrations, and PostgreSQL/MySQL/SQLite Grammars | - |
| **`switch/error-handler`** | [celionatti/switch-error-handler](https://github.com/celionatti/switch-error-handler) | Zero-config error handler, Whoops-style dark debug UI, HTTP exceptions & loggers | - |

---

## 🚀 Quick Start Example

Here is how all packages seamlessly assemble into a complete web application:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Switch\Config\Config;
use Switch\Container\Container;
use Switch\Database\Connection\Connection;
use Switch\Database\ORM\Model;
use Switch\Kernel\App;
use Switch\Router\Router;
use Switch\View\View;

// 1. Configuration
$config = new Config([
    'app' => ['name' => 'My App', 'debug' => true],
    'db' => ['driver' => 'sqlite', 'database' => ':memory:']
]);

// 2. Container
$container = new Container();
$container->instance(Config::class, $config);

// 3. Database
$connection = Connection::sqlite();
Model::setConnection($connection);

// 4. Router
$router = new Router();
$router->get('/', function () {
    return '<h1>Welcome to Switch Framework!</h1>';
});

$router->get('/products/{id}', function ($id) {
    return View::render('product.show', ['id' => $id]);
});

// 5. Run Application Kernel
$app = new App($container, router: $router);
$app->run();
```

---

## 🧪 Testing

Run the entire framework test suite:

```bash
php scratch/run_tests.php
```

All 112 tests pass cleanly across all packages.
