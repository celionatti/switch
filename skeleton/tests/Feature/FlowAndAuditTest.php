<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Switch\Foundation\Flow\AuditTrail;
use Switch\Foundation\Flow\TransitionDeniedException;
use Tests\TestCase;

class FlowAndAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        AuditTrail::clear();
    }

    public function testPostFlowStateTransitions(): void
    {
        /** @var Post $post */
        $post = Post::create([
            'title' => 'Flow Model Test',
            'slug' => 'flow-model-test',
            'content' => 'State machine transitions test.',
            'status' => 'draft',
        ]);

        $this->assertEquals('draft', $post->state());
        $this->assertTrue($post->canApply('publish'));
        $this->assertFalse($post->canApply('archive')); // Cannot archive from draft

        // Transition: draft -> published
        $post->applyFlow('publish');
        $this->assertEquals('published', $post->state());

        // Transition: published -> archived
        $post->applyFlow('archive');
        $this->assertEquals('archived', $post->state());

        // Check available transitions
        $transitions = $post->availableTransitions();
        $this->assertArrayHasKey('draft', $transitions);
    }

    public function testInvalidTransitionThrowsException(): void
    {
        $post = Post::create([
            'title' => 'Invalid Transition',
            'slug' => 'invalid-transition',
            'content' => 'content',
            'status' => 'draft',
        ]);

        $this->expectException(TransitionDeniedException::class);
        $post->applyFlow('archive'); // Cannot directly archive draft
    }

    public function testAuditTrailRecording(): void
    {
        $post = Post::create([
            'title' => 'Audit Logged Post',
            'slug' => 'audit-logged-post',
            'content' => 'content',
        ]);

        $post->recordAudit('viewed', ['ip' => '127.0.0.1']);
        $post->recordAudit('exported', ['format' => 'pdf']);

        $history = $post->history();
        $this->assertCount(2, $history);
        $this->assertEquals('viewed', $history[0]['event']);
        $this->assertEquals('exported', $history[1]['event']);
    }
}
