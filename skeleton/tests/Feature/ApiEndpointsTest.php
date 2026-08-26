<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Switch\Router\Facade\Route;
use Switch\Router\Router;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup clean Router for testing routes
        $router = new Router();
        Route::setRouter($router);

        require __DIR__ . '/../../routes/api.php';
    }

    public function testStatusEndpointReturnsOk(): void
    {
        $this->get('/status')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('framework', 'Switch Framework')
            ->assertJsonStructure(['status', 'framework', 'version', 'timestamp']);
    }

    public function testPostApiCrudLifecycle(): void
    {
        // 1. Create Post via API
        $response = $this->postJson('/posts', [
            'title' => 'API Created Post',
            'content' => 'Content created via Testbench HTTP DSL.',
            'tags' => ['api', 'testbench'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'API Created Post')
            ->assertJsonPath('data.slug', 'api-created-post')
            ->assertJsonPath('data.status', 'draft');

        $postId = (int) $response->json('data.id');
        $this->assertGreaterThan(0, $postId);

        // 2. Read Posts list
        $this->get('/posts')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'posts',
                    'pagination' => ['total', 'per_page', 'current_page'],
                    'tags',
                ]
            ]);

        // 3. Show Post
        $this->get("/posts/{$postId}")
            ->assertOk()
            ->assertJsonPath('data.id', $postId)
            ->assertJsonPath('data.title', 'API Created Post');

        // 4. Publish Post via State Machine
        $this->postJson("/posts/{$postId}/publish")
            ->assertOk()
            ->assertJsonPath('data.status', 'published');

        // Verify state transitioned in database
        $post = Post::find($postId);
        $this->assertNotNull($post);
        $this->assertEquals('published', $post->status);

        // 5. Delete Post
        $this->deleteJson("/posts/{$postId}")
            ->assertOk()
            ->assertJsonPath('data.deleted', true);

        // Verify soft deleted
        $this->assertNull(Post::find($postId));
    }
}
