<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\CreatePostAction;
use App\Models\Post;
use Tests\TestCase;

class ActionTest extends TestCase
{
    public function testActionExecutesAndCreatesModel(): void
    {
        /** @var Post $post */
        $post = CreatePostAction::run([
            'title' => 'Declarative Actions in Switch',
            'content' => 'Actions encapsulate business workflows and domain logic.',
            'tags' => ['action', 'ddd', 'switch'],
            'is_featured' => true,
        ]);

        $this->assertNotNull($post->id);
        $this->assertEquals('declarative-actions-in-switch', $post->slug);
        $this->assertEquals('draft', $post->status);
        $this->assertEquals(['action', 'ddd', 'switch'], $post->tags);

        // Verify audit log
        $audits = $post->audits();
        $this->assertNotEmpty($audits);
        $this->assertEquals('created', $audits[0]['event']);
    }

    public function testActionValidationFailure(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Missing required 'title' and 'content'
        CreatePostAction::run([]);
    }
}
