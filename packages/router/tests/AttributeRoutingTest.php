<?php

declare(strict_types=1);

namespace Switch\Router\Tests;

use PHPUnit\Framework\TestCase;
use Switch\Foundation\Attributes\Authorize;
use Switch\Foundation\Attributes\Cached;
use Switch\Foundation\Attributes\RateLimit;
use Switch\Router\Attributes\Delete;
use Switch\Router\Attributes\Get;
use Switch\Router\Attributes\Middleware;
use Switch\Router\Attributes\Post;
use Switch\Router\Attributes\Put;
use Switch\Router\Attributes\Route as RouteAttr;
use Switch\Router\Router;

// Sample Test Controller with Attributes
#[RouteAttr('/api/v1')]
#[Middleware('api_auth')]
class SampleApiController
{
    #[Get('/users', name: 'api.users.index')]
    #[Cached(ttl: 120, tags: ['users'])]
    public function index(): string
    {
        return 'users_list';
    }

    #[Post('/users')]
    #[Authorize('create_user')]
    #[RateLimit(limit: 30)]
    public function store(): string
    {
        return 'user_created';
    }

    #[Put('/users/{id}')]
    public function update(): string
    {
        return 'user_updated';
    }

    #[Delete('/users/{id}')]
    #[Authorize('delete_user')]
    public function destroy(): string
    {
        return 'user_deleted';
    }
}

class AttributeRoutingTest extends TestCase
{
    public function testScanAttributesRegistersRoutesWithPrefixAndMiddleware(): void
    {
        $router = new Router();
        $router->scanAttributes(SampleApiController::class);

        // 1. Check GET /api/v1/users route
        $match = $router->match('GET', '/api/v1/users');
        $this->assertEquals([SampleApiController::class, 'index'], $match->getHandler());
        $this->assertContains('api_auth', $match->getMiddleware());
        $this->assertEquals('api.users.index', $match->getRoute()->getName());

        // Check Cached attribute attached to route
        $route = $match->getRoute();
        $cachedAttr = $route->getAttribute(Cached::class);
        $this->assertNotNull($cachedAttr);
        $this->assertEquals(120, $cachedAttr->ttl);
        $this->assertEquals(['users'], $cachedAttr->tags);

        // 2. Check POST /api/v1/users route
        $postMatch = $router->match('POST', '/api/v1/users');
        $this->assertEquals([SampleApiController::class, 'store'], $postMatch->getHandler());

        // Check Authorize & RateLimit attributes
        $postRoute = $postMatch->getRoute();
        $authAttr = $postRoute->getAttribute(Authorize::class);
        $this->assertNotNull($authAttr);
        $this->assertEquals('create_user', $authAttr->ability);

        $rateAttr = $postRoute->getAttribute(RateLimit::class);
        $this->assertNotNull($rateAttr);
        $this->assertEquals(30, $rateAttr->limit);

        // 3. Check PUT /api/v1/users/42 with parameter extraction
        $putMatch = $router->match('PUT', '/api/v1/users/42');
        $this->assertEquals('42', $putMatch->getParameters()['id']);

        // 4. URL generation
        $url = $router->url('api.users.index');
        $this->assertEquals('/api/v1/users', $url);
    }
}
