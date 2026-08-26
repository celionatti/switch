<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DatabaseCrudTest extends TestCase
{
    public function testDatabaseCreateAndRead(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Alice Developer',
            'email' => 'alice@switchframework.io',
            'password' => password_hash('secret123', PASSWORD_BCRYPT),
        ]);

        $this->assertNotNull($user->id);
        $this->assertEquals('Alice Developer', $user->name);

        /** @var Post $post */
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Building High-Velocity Apps with Switch',
            'slug' => 'building-high-velocity-apps-with-switch',
            'content' => 'Switch is super fast and modular.',
            'status' => 'published',
            'tags' => ['php', 'framework', 'speed'],
            'is_featured' => true,
        ]);

        $this->assertNotNull($post->id);
        $this->assertEquals('building-high-velocity-apps-with-switch', $post->slug);
        $this->assertEquals(['php', 'framework', 'speed'], $post->tags);
        $this->assertTrue($post->is_featured);

        // Read / Find
        $found = Post::find($post->id);
        $this->assertNotNull($found);
        $this->assertEquals($post->title, $found->title);

        $foundBySlug = Post::where('slug', '=', 'building-high-velocity-apps-with-switch')->first();
        $this->assertNotNull($foundBySlug);
        $this->assertEquals($post->id, $foundBySlug->id);
    }

    public function testDatabaseUpdate(): void
    {
        $post = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original Content',
            'status' => 'draft',
        ]);

        $post->title = 'Updated Title';
        $post->content = 'Updated Content';
        $post->save();

        $refreshed = Post::find($post->id);
        $this->assertEquals('Updated Title', $refreshed->title);
        $this->assertEquals('Updated Content', $refreshed->content);
    }

    public function testDatabaseDeleteAndSoftDelete(): void
    {
        $post = Post::create([
            'title' => 'Temporary Post',
            'slug' => 'temp-post',
            'content' => 'Will be soft deleted',
        ]);

        $id = $post->id;
        $post->delete();

        // Normal query should not find soft-deleted model
        $found = Post::find($id);
        $this->assertNull($found);

        // WithTrashed should find soft-deleted model
        $trashed = Post::withTrashed()->where('id', '=', $id)->first();
        $this->assertNotNull($trashed);
        $this->assertNotNull($trashed->deleted_at);

        // Restore
        $trashed->restore();
        $restored = Post::find($id);
        $this->assertNotNull($restored);
        $this->assertNull($restored->deleted_at);
    }

    public function testDatabaseScopes(): void
    {
        Post::create(['title' => 'Draft 1', 'slug' => 'draft-1', 'content' => 'c', 'status' => 'draft', 'is_featured' => false]);
        Post::create(['title' => 'Draft 2', 'slug' => 'draft-2', 'content' => 'c', 'status' => 'draft', 'is_featured' => true]);
        Post::create(['title' => 'Pub 1', 'slug' => 'pub-1', 'content' => 'c', 'status' => 'published', 'is_featured' => true]);
        Post::create(['title' => 'Pub 2', 'slug' => 'pub-2', 'content' => 'c', 'status' => 'published', 'is_featured' => false]);

        $published = Post::published()->get();
        $this->assertCount(2, $published);

        $featured = Post::featured()->get();
        $this->assertCount(2, $featured);

        $publishedFeatured = Post::published()->featured()->get();
        $this->assertCount(1, $publishedFeatured);
        $this->assertEquals('Pub 1', $publishedFeatured[0]->title);
    }

    public function testDatabaseRelationships(): void
    {
        $user = User::create(['name' => 'John Author', 'email' => 'john@test.com', 'password' => 'secret']);
        $post = Post::create(['user_id' => $user->id, 'title' => 'Author Post', 'slug' => 'author-post', 'content' => 'body']);

        $author = $post->user;
        $this->assertNotNull($author);
        $this->assertEquals('John Author', $author->name);
    }
}
