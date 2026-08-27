<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Flows\PostFlow;
use App\Services\PostService;
use App\Utils\FormatUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Http\Response;
use Switch\Http\ServerRequest;
use Switch\Http\Stream;
use Switch\Kernel\Middleware\SecurityHeadersMiddleware;
use Switch\Router\Facade\Route;
use Switch\Router\Router;
use Tests\TestCase;

class UtilsAndArchitectureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure app/Utils/helpers.php is loaded
        $utilsFile = __DIR__ . '/../../app/Utils/helpers.php';
        if (file_exists($utilsFile)) {
            require_once $utilsFile;
        }

        $router = new Router();
        Route::setRouter($router);
        require __DIR__ . '/../../routes/web.php';
    }

    public function testAutoLoadedUtilsHelpersAvailableGlobally(): void
    {
        // 1. currency()
        $this->assertEquals('$1,249.50', currency(1249.50));
        $this->assertEquals('€99.00', currency(99, '€'));

        // 2. slugify()
        $this->assertEquals('ultra-fast-php-framework', slugify('Ultra Fast PHP Framework!'));
        $this->assertEquals('switch-live-2026', slugify('Switch Live 2026'));

        // 3. mask_email()
        $this->assertEquals('s***r@example.com', mask_email('sarah.connor@example.com'));

        // 4. human_filesize()
        $this->assertEquals('1.00 KB', human_filesize(1024));
        $this->assertEquals('2.50 MB', human_filesize(2621440));

        // 5. str_initials()
        $this->assertEquals('SC', str_initials('Sarah Connor'));
        $this->assertEquals('CN', str_initials('Celio Natti'));
    }

    public function testUtilsClassAutoloading(): void
    {
        $truncated = FormatUtil::truncate('Switch Framework is designed for maximum speed and simplicity.', 25);
        $this->assertEquals('Switch Framework is desig...', $truncated);

        $phone = FormatUtil::phone('1234567890');
        $this->assertEquals('(123) 456-7890', $phone);
    }

    public function testPostServiceInstantiableAndFunctional(): void
    {
        $service = new PostService();
        $this->assertInstanceOf(PostService::class, $service);
    }

    public function testFlowsLifecycleArchitecture(): void
    {
        $flow = PostFlow::create();
        $this->assertEquals('draft', $flow->getInitialState());

        $mockPost = (object) ['status' => 'draft', 'title' => 'Building Fast Web Apps', 'body' => 'Content'];
        $this->assertTrue($flow->can($mockPost, 'publish'));
        $this->assertTrue($flow->can($mockPost, 'submit_for_review'));
        $this->assertFalse($flow->can($mockPost, 'archive'));

        $flow->apply($mockPost, 'publish');
        $this->assertEquals('published', $mockPost->status);
    }

    public function testSecurityHeadersInjectedOnWebRequests(): void
    {
        $middleware = new SecurityHeadersMiddleware();
        $request = new ServerRequest('GET', '/');

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, [], Stream::create('OK'));
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertTrue($response->hasHeader('X-Content-Type-Options'));
        $this->assertEquals('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
        $this->assertEquals('SAMEORIGIN', $response->getHeaderLine('X-Frame-Options'));
        $this->assertEquals('1; mode=block', $response->getHeaderLine('X-XSS-Protection'));
    }
}
