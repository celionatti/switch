<?php

declare(strict_types=1);

namespace App\Flows;

use Switch\Foundation\Flow\StateMachine;

class PostFlow
{
    /**
     * Define the Post state machine lifecycle.
     */
    public static function create(string $field = 'status'): StateMachine
    {
        return StateMachine::define($field)
            ->states(['draft', 'review', 'published', 'archived'])
            ->initial('draft')
            ->allow('submit_for_review', 'draft', 'review')
            ->allow('publish', ['draft', 'review'], 'published', function ($post, $context) {
                // Guard: verify post is ready for publication
                return !empty($post->title);
            })
            ->allow('archive', 'published', 'archived')
            ->allow('reopen', 'archived', 'draft')
            ->allow('draft', 'archived', 'draft');
    }
}
