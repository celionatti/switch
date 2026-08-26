<?php

declare(strict_types=1);

namespace Tests\Feature;

use Switch\Router\Facade\Route;
use Switch\Router\Router;
use Tests\TestCase;

class ShowcaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $router = new Router();
        Route::setRouter($router);

        require __DIR__ . '/../../routes/web.php';
    }

    public function testShowcasePageRenders(): void
    {
        $response = $this->get('/features');

        $response->assertOk()
            ->assertSee('Package & Subsystem Showcase', escape: false)
            ->assertSee('Context API Subsystem')
            ->assertSee('Data & Mock Blueprints', escape: false)
            ->assertSee('Fluent Collection Engine')
            ->assertSee('Bridge Webhook Dispatcher')
            ->assertSee('Mailer & Mailable Engine', escape: false)
            ->assertSee('Passwordless Magic Links');
    }

    public function testShowcaseAliasRouteRenders(): void
    {
        $response = $this->get('/showcase');

        $response->assertOk()
            ->assertSee('Subsystem Showcase');
    }

    public function testGenerateMocksEndpoint(): void
    {
        $response = $this->post('/showcase/mocks', [
            'type' => 'user',
            'count' => 4,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'type' => 'user',
                'count' => 4,
            ]);
    }

    public function testDispatchWebhookEndpoint(): void
    {
        $response = $this->post('/showcase/webhook', [
            'event' => 'payment.received',
            'email' => 'customer@example.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'event' => 'payment.received',
            ]);
    }
}
