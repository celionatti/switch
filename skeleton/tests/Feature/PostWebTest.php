<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Switch\Router\Facade\Route;
use Switch\Router\Router;
use Tests\TestCase;

class PostWebTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $router = new Router();
        Route::setRouter($router);

        require __DIR__ . '/../../routes/web.php';
    }

    public function testPostsIndexPageRenders(): void
    {
        $response = $this->get('/posts');

        $response->assertOk()
            ->assertSee('Knowledge Base')
            ->assertSee('Write New Post');
    }

    public function testPostAliasRouteRenders(): void
    {
        $response = $this->get('/post');

        $response->assertOk()
            ->assertSee('Knowledge Base');
    }

    public function testPostCreatePageRenders(): void
    {
        $response = $this->get('/posts/create');

        $response->assertOk()
            ->assertSee('Write a New Post')
            ->assertSee('Post Title')
            ->assertSee('Publish via Action');
    }

    public function testPostCreationViaFormSubmission(): void
    {
        $response = $this->post('/posts', [
            'title' => 'Web Created Article',
            'content' => 'Full article body written through web controller.',
            'tags' => 'switch, web, blade',
            'is_featured' => '1',
        ]);

        $response->assertStatus(302);

        $post = Post::where('title', 'Web Created Article')->first();
        $this->assertNotNull($post);
        $this->assertEquals('web-created-article', $post->slug);
        $this->assertEquals(['switch', 'web', 'blade'], $post->tags);
        $this->assertTrue($post->is_featured);
    }

    public function testSinglePostShowViewRenders(): void
    {
        $post = Post::create([
            'title' => 'Deep Dive into Switch Views',
            'slug' => 'deep-dive-into-switch-views',
            'content' => 'Detailed explanation of directives, components, and zero-stat compilation.',
            'status' => 'draft',
            'tags' => ['views', 'templates'],
            'is_featured' => true,
        ]);
        $post->recordAudit('created', ['author' => 'developer']);

        $response = $this->get("/posts/{$post->id}");

        $response->assertOk()
            ->assertSee('Deep Dive into Switch Views')
            ->assertSee('Detailed explanation of directives')
            ->assertSee('Finite State Machine (Flow)')
            ->assertSee('Audit Trail History')
            ->assertSee('Publish Post');
    }

    public function testPostPublishWorkflow(): void
    {
        $post = Post::create([
            'title' => 'Draft Post to Publish',
            'slug' => 'draft-post-to-publish',
            'content' => 'This post begins as a draft.',
            'status' => 'draft',
        ]);

        $response = $this->post("/posts/{$post->id}/publish");
        $response->assertStatus(302);

        $fresh = Post::find($post->id);
        $this->assertNotNull($fresh);
        $this->assertEquals('published', $fresh->status);
    }

    public function testPostDeleteWorkflow(): void
    {
        $post = Post::create([
            'title' => 'Post to be deleted',
            'slug' => 'post-to-be-deleted',
            'content' => 'This post will be soft deleted.',
            'status' => 'published',
        ]);

        $response = $this->post("/posts/{$post->id}/delete");
        $response->assertStatus(302);

        $this->assertNull(Post::find($post->id));
        $trashed = Post::withTrashed()->where('id', $post->id)->first();
        $this->assertNotNull($trashed);
    }
}
